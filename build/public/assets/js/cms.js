var cms = new Object();

	cms.page = 1
	cms.action = 'index';
	cms.debug = false;
	cms.offsetTop = 70;
	
	cms.adminPath = '/admin/';
	cms.correspondentPost = '/correspondents/json';
	cms.videoPost = '/videos/json';
	
	//fill fields with proper content
	cms.correspondentsFields =[
		//{postVal:'id' , domNam:"id" , type:"input"},
		{postVal:'name' , domName:"title" , type:"input" },
		{postVal:'fbhandle' , domName:"fb" , type:"input" },
		{postVal:'twitterhandle',domName:"twitterhandle" , type:"input" },
		{postVal:'address' ,domName:"address" , type:"input"}
	];
	
	//will need to add unique types of form/inputs here radioboxs and dropdowns.
	cms.videosFields = [
		//{postVal:'id' , domNam:"id" , type:"input" },
		{postVal:"title", domName:"title" , type:"input" },
		{postVal:"youtubeid", domName:"youtubeid" , type:"input" },
		{postVal:"correspondent", domName:"correspondent" , type:"select"},
		{postVal:"issues", domName:"issues", type:"checkbox", checkboxes:["Education","Corruption","Justice","Woman's Rights","Environment"] },
		{postVal:"posted", domName:"posted" , type:"multi-input" , defaults:["MM","DD","YYYY"] , order:[2,0,1]}
	];
	
	cms.pageFields;
	
	
	///!!!!!FUNCTIONS!!!!
	
	cms.init = function(p,a){
		cms.action = a;
		cms.page = p;
		
		if( cms.action == 'correspondents'){
			cms.pageFields = cms.correspondentsFields;
		}else{
			cms.pageFields = cms.videosFields;
		}
	}
	
	
//NEW ------------
	cms.createNewEntry = function(){
		trace(' create new entry ' + cms.action );
		//clear fields just in case.
		$("#create-new .small-message").html('');
		
		for( var i=0; i< cms.pageFields.length; i++){
			if( cms.pageFields[i].type == 'select'){
				$("#create-"+cms.pageFields[i].domName+"-input select").val('');
			}else if( cms.pageFields[i].type == 'checkbox' ){
				var children = $("#create-"+cms.pageFields[i].domName+"-input li input")
				for( var c=0; c< children.length; c++){
					//trace( children[c] );
					$( children[c] ).attr("checked", false);	
				}			
			}else if(cms.pageFields[i].type == 'multi-input' ){
				for( var mi=0; mi < $("#create-"+cms.pageFields[i].domName+"-input input").length; mi++){
					$($("#create-"+cms.pageFields[i].domName+"-input input")[mi]).val(cms.pageFields[i].defaults[mi]);
				}
			}else{
				$("#create-"+cms.pageFields[i].domName+"-input input").val('');
			}
		}
			
		$("#lightbox").fadeIn(10);
		$("#create-new").fadeIn(100);
	}
	
	cms.newSave = function(){
		var error = false;
		var url = cms.adminPath + 'add';
		
		var data = new Object();
		//fill fields with proper content
		for( var i=0; i< cms.pageFields.length; i++){
			if( cms.pageFields[i].type == 'select'){
				data[ cms.pageFields[i].postVal ] = $("#create-"+cms.pageFields[i].domName+"-input select").val();
			}else if( cms.pageFields[i].type == 'checkbox' ){
				//loop through checkboxes
				data[ cms.pageFields[i].postVal ] = '';
				var children = $("#create-"+cms.pageFields[i].domName+"-input li input")
				for( var c=0; c< children.length; c++){
					var isChecked = $( children[c] ).is(":checked");
					trace(' is checked ' + isChecked + ' ---- ? ');
					if( isChecked ){
						data[ cms.pageFields[i].postVal ] += '1'
					}else{
						data[ cms.pageFields[i].postVal ] += '0'
					}
				}
			}else if(cms.pageFields[i].type == 'multi-input' ){
				var milength = cms.pageFields[i].order.length;//$("#create-"+cms.pageFields[i].domName+"-input input").length;
				var multiInputTxt = '';
				for( var mi=0; mi < milength; mi++){
					var nextInOrder = cms.pageFields[i].order[mi];
					var inputValue = $( $("#create-"+cms.pageFields[i].domName+"-input input")[nextInOrder] ).val();
					if( inputValue.length == 1 ){
						inputValue = '0'+inputValue;
					}
					multiInputTxt += inputValue
					if( mi < (milength-1) ){
						multiInputTxt += '-';	
					}
				}
				data[cms.pageFields[i].postVal ] = multiInputTxt;
			}else{
				data[ cms.pageFields[i].postVal ] = $("#create-"+cms.pageFields[i].domName+"-input input").val();
			}
		}
		
		//check for specific errors per type:
		if( cms.action == 'correspondents'){
			if( (data.name.length<1) ){
				error = 'Name is required.';
			}else if( (data.fbhandle.length<2) && (data.twitterhandle.length<2) ){
				error = 'You must fill out either the Facebook or Twitter information.';
			}
			
			url += cms.correspondentPost;
		}else{
			//check for valid posted date
			//need all values full.
			
			trace( ' ---- posted data' );
			trace( data );
			trace('------');
			
			if(data.title.length < 3 ){
				error = 'Title is Too Short';
			}else if( data.youtubeid.length < 3 ){
				error = 'Not a valid youtube ID';
			}else if( (data.posted.length<10 || data.posted == 'MM-DD-YYYY' ) ){
				error = 'Valid Date Required MM-DD-YYYY';
			}else if(data.issues == '00000'){
				error = 'No issues defined';
			}else if(data.correspondent == -1){
				error = 'No correspondent selected';
			}
			
			url += cms.videoPost;
		}
		
		if(error){
			cms.newDisplayError(error)
		}else{
			cms.displayLoader();
			$.post(url , data , cms.newSaveComplete );
		}
	}
	
	cms.newDisplayError = function(er){
		$("#create-new .small-message").html(er);
	}
	cms.newSaveComplete = function(data){
		trace(data);
		//cms.hideLoader();
		$("#loading-content").fadeOut(10);
		$("#new-success").fadeIn(100);	
	}
	cms.closeSuccessMessage = function(){
		$("#new-success").fadeOut(100);	
		$("#lightbox").fadeOut(100);
		
		$("#refreshPage").fadeIn(100);
	}
	
	cms.newCancel = function(){
		$("#create-new").fadeOut(100);
		$("#lightbox").fadeOut(100);
	}

//UPDATE ------------	
	cms.updateItem = function(id){
		trace(' update item ' + id );
		
		var top = $("#entry_"+id ).offset().top - cms.offsetTop;
		$("#edit").css({'top':top});
		$("#edit").fadeIn(100);
		$("#lightbox").fadeIn(10);
		
		$("#edit #edit-error").css({'display':'none'});
		
		
		$("#edit-id-input input").val(id);
		for(var i=0; i < cms.pageFields.length; i++){
			if( cms.pageFields[i].type == 'select'){
				var selectField = $("#entry_"+id+" ."+cms.pageFields[i].domName ).data('val'); 
				$( "#edit-"+cms.pageFields[i].domName + "-input select" ).val( selectField );
			}else if(cms.pageFields[i].type == 'checkbox'){
				var checkboxData = $("#entry_"+id+" ."+cms.pageFields[i].domName ).data(cms.pageFields[i].domName); 
				checkboxData = String(checkboxData).split('');
				
				$("#edit-"+cms.pageFields[i].domName+"-input input").attr("checked",false) 
				var inputs = $("#edit-"+cms.pageFields[i].domName+"-input input");
				for( var cb=0; cb < inputs.length; cb++){
					var checked = checkboxData[ cb ];
					if( checked == '1' ){
						$( inputs[cb] ).attr("checked",true) 
					}else{
						$( inputs[cb] ).attr("checked",false) 
					}
				} 				
			}else if(cms.pageFields[i].type == 'multi-input'){
				//trace( cms.pageFields[i].domName );
				if( cms.pageFields[i].domName == 'posted' ){
					var date = $("#entry_"+id+" ."+cms.pageFields[i].domName + ' span').html(); 
					var dateArray = date.split('-');
					//trace( dateArray);
					for( var d=0; d < cms.pageFields[i].order.length ; d++){
						$( $("#edit-"+cms.pageFields[i].domName+"-input input")[ cms.pageFields[i].order[d] ] ).val(dateArray[ d ] ) 
					}
				}
			}else{
				var field = $("#entry_"+id+" ."+cms.pageFields[i].domName+" span" ).html();
				$( "#edit-"+cms.pageFields[i].domName + "-input input" ).val( field );
			}
		}
		
	}
	
	cms.updateSave = function(){
		var error = false;
		$("#edit #edit-error").css({'display':'none'});
		
		//post to db
		var url = cms.adminPath + 'update';
		var data = new Object();
		
		data.id = $("#edit-id-input input").val();
		
		if( cms.action == 'correspondents' ){
			url += cms.correspondentPost;
		}else if( cms.action == 'videos'){
			url += cms.videoPost;
			//check for valid posted date.
			
		}
		
		for(var i=0; i < cms.pageFields.length; i++){
			if( cms.pageFields[i].type == 'input' ){
				data[ cms.pageFields[i].postVal ] = $("#edit-"+cms.pageFields[i].domName+"-input input").val();
				$("#entry_"+data.id +' .'+cms.pageFields[i].domName+' span ').html(data[ cms.pageFields[i].postVal ]);
				
			}else if( cms.pageFields[i].type == 'select' ){
				//assign to data for post
				var selectID = $("#edit-"+cms.pageFields[i].domName+"-input select").val();
				data[ cms.pageFields[i].postVal ] = selectID;
				
				//assign/update the DOM listing
				var valueName = $("#edit-"+cms.pageFields[i].domName+"-input select option[value='"+selectID+"']").html();
				$("#entry_"+data.id +" ."+cms.pageFields[i].domName).data('val', selectID );
				$("#entry_"+data.id +' .'+cms.pageFields[i].domName+' span ').html( valueName );
				
			}else if( cms.pageFields[i].type == 'checkbox' ){
				var inputs = $("#edit-"+cms.pageFields[i].domName+"-input input");
				var datastring = '';
				//if it is issues use the abreviated string list
				var issuesConverted = '';
				for( var cb=0;cb< inputs.length;cb++){
					if( $( inputs[cb] ).attr("checked") ){
						datastring += '1';
						if( cms.pageFields[i].domName == 'issues' ){
							issuesConverted += issuesAbreviated[cb]+", ";
						}
					}else{
						datastring += '0';
					}			
					//clear all previous regardless
					//$( inputs[cb] ).attr("checked",false) 
				}
				
				//assign to the data post
				data[cms.pageFields[i].postVal] = datastring;
				$("#entry_"+data.id+" ."+cms.pageFields[i].domName ).data('issues', datastring );
				trace( $("#entry_"+data.id+" ."+cms.pageFields[i].domName ).data() );
				
				if( cms.pageFields[i].domName == 'issues' ){
					//remove last comma
					issuesConverted = issuesConverted.slice(0, -2);
					$("#entry_"+data.id+" ."+cms.pageFields[i].domName + ' span ').html(issuesConverted);
				}
				
			}else if(cms.pageFields[i].type == 'multi-input'){
				//get the date posted
				if( cms.pageFields[i].domName == 'posted' ){
					var dateConcated = '';
					var dlength = cms.pageFields[i].order.length;
					for( var d=0; d < dlength ; d++){
						 var inputValue = $( $("#edit-"+cms.pageFields[i].domName+"-input input")[ cms.pageFields[i].order[d] ] ).val(); 
						if( inputValue.length == 1 ){
							inputValue = '0'+inputValue;
						}
						dateConcated += inputValue
						if( d < (dlength-1) ){
							dateConcated += '-';	
						}
					}
					//dateConcated = dateConcated.slice(0,-1);
					$("#entry_"+data.id+" ."+cms.pageFields[i].domName + ' span').html(dateConcated); 
					data[cms.pageFields[i].postVal] = dateConcated;
				}							
			}
		}
		
		if( cms.action == 'correspondents' ){
		
		}else if( cms.action == 'videos' ){
			//check for errors:
			if(data.title.length < 3 ){
				error = 'Title is Too Short';
			}else if( data.youtubeid.length < 3 ){
				error = 'Not a valid youtube ID';
			}else if( (data.posted.length<10 || data.posted == 'MM-DD-YYYY' ) ){
				error = 'Valid Date Required MM-DD-YYYY';
			}else if(data.issues == '00000'){
				error = 'No issues defined';
			}else if(data.correspondent == -1){
				error = 'No correspondent selected';
			}
		}
		
		trace( data );
		
		if(error){
			cms.editDisplayError(error);
		}else{
			cms.displayLoader();
			$.post( url , data , cms.updateComplete );
		}
		
	}
	cms.editDisplayError = function(error){
		trace( error );
		$("#edit #edit-error").fadeIn();
		$("#edit #edit-error").html('Error: '+error);
			
	}
	
	cms.updateComplete = function(data){
		trace(data);
		//check for error?
		if( data.page == 'video'){
			trace( $("#entry_"+data.id) );
			$("#entry_"+data.id).removeClass("inactive");
		}
		
		cms.hideLoader();
	}

	cms.updateCancel = function(){
		$("#edit").fadeOut(100);
		$("#lightbox").fadeOut(200);
	}


///DELETE ------------
	cms.deleteEntry = function(id){
		var top = $("#entry_"+id ).offset().top - cms.offsetTop ;
		$("#confirm-delete").css({'top':top});
		$("#confirm-delete").data("id", id );
		$("#confirm-delete .message").html("Are you sure you want to delete " + $("#entry_"+id +' .title span ').html() )
		$("#confirm-delete").fadeIn(100);
		$("#lightbox").fadeIn(10);
	}
	cms.deleteCancel = function(){
		$("#confirm-delete").fadeOut(100);
		$("#lightbox").fadeOut(10);
	}
	cms.deleteConfirm = function(){
		cms.displayLoader();
		//post to db
		var url = cms.adminPath + 'delete';
		var data = new Object();
			data.id = $("#confirm-delete").data("id");
			
		if( cms.action == 'correspondents' ){
			url += cms.correspondentPost;
		}else if( cms.action == 'videos'){
			url += cms.videoPost;
		}
		$.post( url , data , cms.deleteComplete );
	}
	
	cms.deleteComplete = function(data){
		trace(data);
		//check for error?
		cms.hideLoader();
		$("#entry_"+data.id).fadeOut();
		$("#entry_edit_box_"+data.id).fadeOut();
		$("#entry_"+data.id).parent().css({"background-image":"url('/assets/images/cms/stripe.png')"});
	}
	
//LOADER ------------
	cms.displayLoader = function(){
		//one of these would be active. 
		//just in case there is an error.. hide em all from view
		
		$("#confirm-delete").fadeOut(100);
		$("#edit").fadeOut(100);
		$("#create-new").fadeOut(100);
		
		$("#loading-content").fadeIn(10);
	}
	
	cms.hideLoader = function(){
		$("#loading-content").fadeOut(10);
		$("#lightbox").fadeOut(100);
	}


/////-------- EVENTS ------------- ///////	

//ADD EVENTS
	$("#addentry").click(function(){
		//will call the proper function depending on videos.phtml / correspondents.phtml
		//<?php echo $this->action; ?> //needed
		cms.createNewEntry(); 
	});
		$("#create-new .save-cancel").click(function(){
			cms.newCancel();
		});
		$("#create-new .save-changes").click(function(){
			cms.newSave();
		});
		$("#new-success").click(function(){
			cms.closeSuccessMessage();
		});
	$("#refreshPage").click(function(){
		location.reload(true);
	});	
	
//DELETE EVENTS
	$(".delete-item").click(function(){
		cms.deleteEntry( $(this).data('id') );
	});
	$(".delete-item").click(function(){
		cms.deleteEntry( $(this).data('id') );
	});
		$("#confirm-delete .delete-cancel").click(function(){
			cms.deleteCancel();
		});
		$("#confirm-delete .delete-confirm").click(function(){
			cms.deleteConfirm();	
		});

//EDIT EVENTS
	$(".list-item").click(function(){
		cms.updateItem( $(this).data('id') );
	});
	$(".edit-item").click(function(){
		cms.updateItem( $(this).data('id') );
	});
	
		$("#edit .save-cancel").click(function(){
			cms.updateCancel();
		});
		$("#edit .save-changes").click(function(){
			cms.updateSave();
		});




function trace(str){
	if(cms.debug == true ){
		console.log(str);
	}
}