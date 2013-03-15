/*
slider days => display values (XX times per month)

1 => 30
2 => 15
3 => 10
6 => 5
10 => 3
15 => 2
30 => 1


*/
//------------------
//------------------ SINGELTON OBJECTS
//------------------
var Debug = Em.Object.create({
	debug:true,

	trace:function(str){
		if( Debug.debug == true ){
			try{
				console.log(str);
			}catch(err){
				//error
			}
		}
	}
});

//ADD ANY PARAMS IN HERE >>>>
//OR general functions to use


var masterModel = Em.Object.create({
	root: "",
	assets: "assets/",
	counterTopFH:28,
	counterBotFH:46,
	counterAllFW:38,
	counterBottomOffset:279,
	
	FBcounterInc:25,
	FBcounterPace:200,
	
	youtubeW:640,
	youtubeH:360,
	
	init:function(){
		//Debug.trace('---- master Model setup ' );
	},
	addCommas:function(num){
		var str = num.toString();
		//based off of http://www.mredkj.com/javascript/nfbasic.html
		var commaExp = /(\d+)(\d{3})/;
		while (commaExp.test(str)) {
			str = str.replace(commaExp, '$1' + ',' + '$2');
		 }
		return str;	
	},
	scrollTo:function(id){
		var _y = $("#"+id ).offset().top;
		$('html body').animate({ scrollTop: $("#"+id ).offset().top	}, 2000);
		if( FB ){
			FB.Canvas. scrollTo( 0 , _y );
		}
		Debug.trace(' parent SCROLLLING - ' + _y );
		//window.parent.$("body").animate({scrollTop: $("#"+id ).offset().top }, 'slow');
	},
	//http://stackoverflow.com/questions/1199352/smart-way-to-shorten-long-strings-with-javascript
	truncateString:function(str, characterCount){
		var toLong = str.length>characterCount;
     	var _str = toLong ? str.substr(0,characterCount-1) : str;
 		_str = toLong ? _str.substr(0,_str.lastIndexOf(' ')) : _str;
 		return  toLong ? _str + '...' : _str;
	}
});

//zero counter generator
var zeroCounterBkgd = Em.Object.create({
	//length:null,
	//id:null,
	
	comma: '<ul class="cd"><li class="s"></li></ul>',
	
	generateHTML:function(id , length , color ){
		
		var height; 
		if(color=="white") height = "-280px";
		else if(color=="black") height = "-279px";
		
		var html = '<div id="'+id+'" class="counter-bkgdZeros flip-counter" >';
		var zero =  '<ul class="cd" ><li class="t" style="background-position: 0px 0px; "></li><li class="b" style="background-position: 0px ' + height + '; "></li></ul>';
		//var container = new Array();
		var count = 0;
		for( var z=0; z < length; z++){
			if( count == 3 ){
				html += this.comma
				count = 0;
			}
			html += zero;
			count++
		}
		
		html += '</div>';
		return html;
	}	//.property('zero', 'comma' )
	
})


//------------------
//------------------ OBJECT CLASSES to be reused
//------------------

Youtube = Em.Object.extend({
	embedID:null,
	width:"640",
	height:"360",
	iframeID:null,
	autoplay:"0",
	
	embed:function(){
		Debug.trace(' EMBED CALLED ' + this.embedID );

		return '<iframe id="'+ this.iframeID +'" width="'+this.width+'" height="'+this.height+'" src="http://www.youtube.com/embed/'+this.embedID+'?autoplay='+this.autoplay+'" frameborder="0" allowfullscreen></iframe>';
	}.property('embedID' , 'width' , 'height' ),
	
	callPlayer:function(func) {
	
		if (window.jQuery && this.iframeID instanceof jQuery) this.iframeID = this.iframeID.get(0).id;
	    var iframe = document.getElementById(this.iframeID);
	    if (iframe && iframe.tagName.toUpperCase() != 'IFRAME') {
	        iframe = iframe.getElementsByTagName('iframe')[0];
	    }
	    if (iframe) {
	        // Frame exists,
	        iframe.contentWindow.postMessage(JSON.stringify({
	            "event": "command",
	            "func": func,
	            "args": [],
	            "id": this.iframeID
	        }), "*");
	    }
		
	}	
	
})


//------------------
//------------------ VIEWS
//------------------

//---- TOP HEADER
var HeaderView = Em.View.create({
	templateName: 'vvHeader',
	logoUrl: masterModel.assets+'images/icons/vv-logotype.png',
	copy:"We train everyday citizens in the developing world to be journalists<br/>who report on issues that would otherwise go unheard."
})

//---- VIDEO VIEW
var VideoView = Em.View.create({
	templateName: 'vvVideoView',
  	
  	sofar:"So far", //so far XXX people have helped us raise : $ XXX in facebook media
  	peopleHave:"people have<br/>contributed the equivalent of",

  	numberPeople:0,
	numberRaised:0,
  	moneyCounter:null,
	currentCount:null,

  	tickerCopy: Ember.computed(function(key, value) {
    	// getter
      	if (arguments.length === 1) {
        return this.get('sofar') +' <span class="orangeFont font-numberBig">'+masterModel.addCommas(this.get('numberPeople'))+'</span> '+this.get("peopleHave");
      	// setter
      	} else {
        	this.set('numberRaised', value);
        	if( moneyCounter != null ){ 
        		moneyCounter.incrementTo(value, 10, 600);
        	}
        	return this.get('sofar') +' <span class="greenFont font-numberBig">'+masterModel.addCommas(value)+'</span> '+this.get("peopleHave");
      	}
	}).property('sofar','peopleHave'), //,'numberRaised','numberPeople', 
  	
  	videoPlayerState:'notclicked',
  	videoPlayer: Ember.computed(function(key,value){
		var thisHTML = '<div id="introVideo-playBtn" class="btn-img" ></div>';
  		if( this.videoPlayerState == 'clicked'){
  			var youtubeVideo = Youtube.create();
  				youtubeVideo.set('embedID' , 'd_daBZNvfjw');
  				youtubeVideo.set('width', 780);
  				youtubeVideo.set('height', 466);
  				youtubeVideo.set('autoplay', 1);
  			thisHTML = youtubeVideo.get('embed');
  		}
  		return thisHTML;
  	}).property('videoPlayerState'),

	
  init:function(){
 	//Debug.trace(' --- View init ---- ');
 	this._super();
  },
  
  didInsertElement:function(){

  	this.counterSetup(this.numberRaised);
  	var htmlzeros = zeroCounterBkgd.generateHTML('videoCounterZeros' , 6 , "white" );
  	$("#counterContainer").append(htmlzeros);
  	
  	$("#vv-Header-content").click(function(){
  		masterModel.scrollTo("vv-FBContainer");
  	});

  	$("#introVideo").bind('click' , VideoView.playIntroClicked);

  },

  playIntroClicked:function(event){
  	Debug.trace(' INTRO VIDEO CLICKED ');
 	VideoView.set('videoPlayerState', 'clicked' );
  	$("#introVideo").unbind('click' , VideoView.playIntroClicked )

  	trackEvent('click', 'PlayMainVideo');
  },
  
  counterSetup:function(num){
  	//Debug.trace( this.numberRaised );
  	this.set('currentCount' , num);
  	this.moneyCounter = new flipCounter('counter-totalMoney', {value:000000, inc:100, pace:1200, auto:false,
  		fW:masterModel.counterAllFW,  
	  	tFH:masterModel.counterTopFH,
  		bFH:masterModel.counterBotFH,
	  	bOffset:masterModel.counterBottomOffset+1		  	
  	 });
  	this.moneyCounter.incrementTo( this.get('currentCount') , 5, 50 );
  	//Debug.trace(this.get('currentCount'));
  }
  
});


var FbView = Em.View.create({
	templateName:"vvFBview",
	isConnected:false,
	
	currentUserState:0,	//0 =  default landing/new user  //1 = submitted thank you page , //2 = logged in/returning users //3 = how it works diagram

	fbviewSmallTitle:"",
	fbviewTitle:"",
	fbviewDescription:"",

	smallTitles:[
					"how much are",
					" ",
					"How much are <span class='greenFont' >your</span> facebook posts worth?" ,
					" "
					],
	titles:[	
				"<span class='greenFont' >YOUR</span> FACEBOOK POSTS WORTH?",
				"<span class='greenFont' >Thank you</span>" , 
				"To a <span class='orangeFont' >journalist</span> in the developing world?",
				"How it <span class='greenFont'>Works?</span>"
				],
	descriptions:[
				//"By becoming an affiliate and installing our Facebook app, you'll automatically post <br>Video Voices videos to your Facebook news and help give a voice to the voiceless.",
				"By installing our Facebook app, you'll automatically post <br>Video Voices content to your Facebook wall.",
				"For becoming an affiliate in The Newsfeed Network",
				"Configure your post settings below to find out. Then install our Facebook App <br>to automatically post video voices to your Facebook Newsfeed over time.",
				"This number is based on how many of your friends see your Facebook posts [1], the number of Facebook friends you have, <br>your frequency settings, and the estimated cost per thousand impressions of a sponsored Newsfeed advertisement [2]."
				],

	headerTopPos:[
					33,40,33,30
				],

	headerMessage:function(){

		//reset position for animation.
		$("#FBmessage-container").css({'top':200 });
		
		var header = '<div id="FBsmallTitle" class="font-subtitle-mini" >'+this.get('smallTitles')[ this.get('currentUserState') ]+'</div>';
			header += '<div id="FBTitle" class="font-title">'+this.get('titles')[ this.get('currentUserState') ]+'</div>';
			header += '<div id="FBDescription" class="font-mediumWeight" >'+this.get('descriptions')[ this.get('currentUserState') ]+'</div>';
		
		//animate
		$("#FBmessage-container").animate({
													'top': this.get('headerTopPos')[ this.get('currentUserState') ] 
													});

		return header;

	}.property('currentUserState'),

	statesTopPosition:[
						{submitted:-460, counter:0, infographic:460 }, //default
						{submitted:0,  counter:460, infographic:460 }, //submitted
						{submitted:-460, counter:0, infographic:460 }, //returning user
						{submitted:-460, counter:-460, infographic:0 } //infographic
						],
	statesAlpha:[
				{submitted:0,counter:1,infographic:0},
				{submitted:1,counter:0,infographic:0},
				{submitted:0,counter:1,infographic:0},
				{submitted:0,counter:0,infographic:1}
				],

	submitted:{	
				msgtop:"Over the next year your posts will help us reach",
				msgmid:"people on Facebook. That's the equivalent of",
				msgbottom:"of Facebook media!",
				msgfooter:"But don’t stop there. A financial contribution donation of just $100 buys a video camera <br>for a community correspondent capture to capture breaking news in the 3rd world.",
				donate:"Donate Money"
			},

	infographicurl:masterModel.assets + 'images/ui/infographic.png',
	infographicColumns:[
						{title:"Frequency" , body:"How often you’ll <br>post to your wall." },
						{title:"Friends" , body:"How many friends<br>will see those <a href='http://techcrunch.com/2012/02/29/facebook-post-reach-16-friends/' target='_blank'>posts</a>*." },
						{title:"Impressions" , body:"That gives us your <br>projected impressions." },
						{title:"Facebook" , body:"How much those impressions are <a href='http://www.unifiedsocial.com/' target='_blank'>worth</a>*." },
						{title:"Media Value" , body:"That gives us your <br>projected media donation." }
						],
	closeInfographic:'Close',

	postsSliderID:"FB-slider-posts",
	postsIcon:masterModel.assets+"images/icons/fb-postfrequency-title.png",
	postsCopy:"How many posts do<br>you want to contribute?",
	postsLabel:"per month",
	postsValueDB:2, //convert to postsValue on start.
	postsValue:15, //updated by the slider as it is used
	postsFactorizer:3,//factor the default 30 per month to a higher increment value by this factor*30= (creates a smoother animation on the user side.)
	postsValueArray:[
		{db:1, label:30 },
		{db:2, label:15 },
		{db:3, label:10 },
		{db:6, label:5  },
		{db:10, label:3 },
		{db:15, label:2 },
		{db:30, label:1 }
		//,{db:0 , label:0 }
	],
	
	friendsSliderID:"FB-slider-friends",
	friendsIcon:masterModel.assets+"images/icons/fb-friends-title.png",
	friendsCopy:"Roughly, how many Facebook friends do you have?",
	friendsLabel:"friends",
	friendsValue:50, //updated by the slider as it is used //friendsNumberTotal:500, //modifiable value on start
	
	issuesIcon:masterModel.assets+"images/icons/fb-issues-title.png",
	issuesCopy:"What issues would you like posted to your Newsfeed?",
	issuesLabels:[
		{label:"Education" , icon:"issuesButton" , id:'vv-fb-rb-edu' },
		{label:"Corruption" , icon:"issuesButton" , id:'vv-fb-rb-corruption'},
		{label:"Justice" , icon:"issuesButton", id:'vv-fb-rb-justice'},
		{label:"Woman's Rights" , icon:"issuesButton" , id:'vv-fb-rb-worights'},
		{label:"Environment" , icon:"issuesButton" , id:'vv-fb-rb-environment'}
	],
	issuesValue:'11111',// 00000
	userFB:0, //this is updated only by the server if the php api gets a success match with the DB.

	mediaCounterLabel:"Your projected media value per year",
	impressionsCounterLabel:"Your projected impressions donated per year",
	
	howthisworks:"How this works",

	mediaCounter:null,
	impressionsCounter:null,
	
	impressionsCounterSubmitted:null,
	mediaCounterSubmitted:null,

	init:function(){
		this._super();
	},
	
	didInsertElement:function(){
		this.mediaCounterSetup();
		this.impressionsCounterSetup();	
		this.submitPageCounterSetup();

		for( var p=0; p < this.postsValueArray.length; p++){
			if( this.postsValueDB == this.postsValueArray[p].db ){
				this.postsValue = this.postsValueArray[p].label;
			}
		}
		
		var impressionszeros = zeroCounterBkgd.generateHTML('impressionsCounterZeros' , 6 , "black");
		$("#counter-impressions-container").append(impressionszeros);
		var mediazeros = zeroCounterBkgd.generateHTML('mediaCounterZeros' , 4 , "black" );
		$("#counter-media-container").append(mediazeros);
		
		var savedZeros = zeroCounterBkgd.generateHTML('counter-media-submitted-zeros' , 4 , "black");
  		$("#submitted-counter-media-wrapper").append(savedZeros);
  		var savedZeros2 = zeroCounterBkgd.generateHTML('counter-impressions-submitted-zeros' , 6 , "black");
  		$("#submitted-counter-impressions-wrapper").append(savedZeros2);
  	
		
		$("#howThisWorksLink").click( FbView.displayInfoGraphicState );
		$("#FB-info-close").click( FbView.displaySetupState )
		
		$("#FB-big-btn").click(FbView.bigButtonClicked)
		$("#FB-big-btn-back").click(FbView.bigButtonClicked)

		$("#FB-submit-donate").click(FbView.donateClicked);

		Debug.trace(' set post value to : ' + this.postsValue + ' set num of FB friends to: ' + this.friendsValue );
		
		this.sliderSetup(this.postsSliderID , this.postsValue*FbView.postsFactorizer ,  30*FbView.postsFactorizer , this.postsLabel , 'postsValue');
		

		//setup radio buttons
		this.setupRadioButtons();
		
		if( FbView.isConnected ){
			Debug.trace(' IS CONNECTED TO FB' );
			this.displayStateNoAnimate(2);
			this.fixFriendSlider(this.friendsValue , true);

			//update expiration of access token
			
			$.post(masterModel.root+'/facebook/updatetoken/json', function(data){
				Debug.trace(' ---- updated access token complete');
				Debug.trace(data);
			});

		}else{
			Debug.trace(' IS NOT CONNECTED TO FB' );		
			this.sliderSetup(this.friendsSliderID , 50 , 100 , this.friendsLabel , 'friendsValue' );
			this.displayStateNoAnimate(0);
		}

	},

	displaySetupState:function(){
		
		//either state 0 or 2
		var setState = 0;
		if( FbView.isConnected ){
			setState = 2;
		}

		FbView.set('currentUserState', setState );
		$("#FB-state-submitted").animate({"top": FbView.statesTopPosition[setState].submitted , 'opacity':FbView.statesAlpha[ setState ].submitted })
		$("#FB-state-counter").animate({"top": FbView.statesTopPosition[setState].counter , 'opacity':FbView.statesAlpha[ setState ].counter })
		$("#FB-state-infographic").animate({"top": FbView.statesTopPosition[setState].infographic , 'opacity':FbView.statesAlpha[ setState ].infographic })

		FbView.updateBigButton();
	},

	displaySavedState:function(returning){
		FbView.set('currentUserState', 1);

		FbView.submitCounterUpdate();

		$("#FB-state-submitted").animate({"top": FbView.statesTopPosition[1].submitted , 'opacity':FbView.statesAlpha[ 1 ].submitted })
		$("#FB-state-counter").animate({"top": FbView.statesTopPosition[1].counter , 'opacity':FbView.statesAlpha[ 1 ].counter })
		$("#FB-state-infographic").css({"top": FbView.statesTopPosition[1].infographic , 'opacity':FbView.statesAlpha[ 1 ].infographic })

		//change the button
		FbView.updateBigButton();

		trackEvent('click','saveduserdata' );
	},

	displayInfoGraphicState:function(){
		FbView.set('currentUserState', 3);
		
		//regardless submitted should be above.
		$("#FB-state-submitted").css({"top": FbView.statesTopPosition[3].submitted , 'opacity':FbView.statesAlpha[ 3 ].submitted })
		$("#FB-state-counter").animate({"top": FbView.statesTopPosition[3].counter , 'opacity':FbView.statesAlpha[ 3 ].counter })
		$("#FB-state-infographic").animate({"top": FbView.statesTopPosition[3].infographic , 'opacity':FbView.statesAlpha[ 3 ].infographic })
		
		FbView.updateBigButton();

		trackEvent('click','viewhowitworks' );
	},
	
	displayStateNoAnimate:function(setState){

		FbView.set('currentUserState', setState);
		Debug.trace(setState + ' --- ' + FbView.statesTopPosition[ setState ].submitted );

		$("#FB-state-submitted").css({"top": FbView.statesTopPosition[ setState ].submitted , 'opacity':FbView.statesAlpha[ setState ] })
		$("#FB-state-counter").css({"top": FbView.statesTopPosition[setState].counter , 'opacity':FbView.statesAlpha[ setState ].counter })
		$("#FB-state-infographic").css({"top": FbView.statesTopPosition[setState].infographic , 'opacity':FbView.statesAlpha[ setState ].infographic })

		FbView.updateBigButton();
	},

	updateBigButton:function(){
		//update the image graphic state of the button
		if( FbView.currentUserState!=3 && FbView.currentUserState !=1 ){
				var btnClass = '';
				if( FbView.currentUserState == 0 ){
					//default
					btnClass = 'bigbtn-state-connect';
				}else if( FbView.currentUserState == 2){
					//returning user 'save your settings'
					btnClass = 'bigbtn-state-save';
				}
				//update bkgd image
				
				$("#FB-big-btn").removeClass().addClass('btn-img '+btnClass);

		}else{
			//is infographic so DO NOT update the button graphic.
			//is back page so display the other button instead.
		}

		//show or hide the FB button.
		if( FbView.currentUserState == 3 || FbView.currentUserState == 1 ){
			//recess the button on the infographic page
			$("#FB-big-btn").css({"top":70});
			if( FbView.currentUserState == 1 ){
				$("#FB-big-btn-back").css({"top":''});
			}else{
				$("#FB-connect-shadow").fadeOut();
			}
		}else{
			$("#FB-big-btn").css({"top":''});
			$("#FB-connect-shadow").fadeIn();
			$("#FB-big-btn-back").css({"top":65});
		}
	},

	setupRadioButtons:function(){
		$("#FB-radioBoxes-issues").buttonset({
			create: function(event, ui){
				FbView.defineRadioButtonsSelected();
				$("#FB-radioBoxes-issues").fadeIn();
			}
		});
		
		$( $("#FB-radioBoxes-issues div")[3] ).css({'padding-left':'25px'});
		
		$("#FB-radioBoxes-issues :checkbox").click(function(){
			var numberChecked = 0;
			for( var k=0; k < $("#FB-radioBoxes-issues :checkbox").length ; k++){
				if( $( $("#FB-radioBoxes-issues :checkbox")[k] ).attr('checked') == 'checked' ){
					Debug.trace(k + ' checked ' );
					FbView.issuesLabels[k].checked = true;	
				}else{
					FbView.issuesLabels[k].checked = false;
				}
				if( FbView.issuesLabels[k].checked == true ){
					numberChecked++
				}
			}
			Debug.trace( ' numberChecked ' + numberChecked );
		});
	},
	defineRadioButtonsSelected:function(){
		var issuesArray = FbView.issuesValue.toString().split("");
			for( var i = 0; i < issuesArray.length ; i++){
				if( issuesArray[i] == '1' ){
					$( $("#FB-radioBoxes-issues :checkbox")[i] ).attr('checked', 'checked');
					FbView.issuesLabels[i].checked = true;	
				}
			}
			$("#FB-radioBoxes-issues").buttonset('refresh');
	},

	mediaCounterSetup:function(){
		this.mediaCounter = new flipCounter('counter-media', {value:000000, auto:false, 
		  inc:masterModel.FBcounterInc, 
		  pace:masterModel.FBcounterPace , 
		  fW:masterModel.counterAllFW,  
		  tFH:masterModel.counterTopFH,
		  bFH:masterModel.counterBotFH,
		  bOffset:masterModel.counterBottomOffset		  	
		});
	},
	impressionsCounterSetup:function(){
		this.impressionsCounter = new flipCounter('counter-impressions', {value:000000, auto:false,
		inc:masterModel.FBcounterInc, 
		pace:masterModel.FBcounterPace , 
		fW:masterModel.counterAllFW,  
		tFH:masterModel.counterTopFH,
		bFH:masterModel.counterBotFH,
		bOffset:masterModel.counterBottomOffset		  	
		 });
	},
	submitPageCounterSetup:function(){
		this.impressionsCounterSubmitted = new flipCounter('counter-impressions-submitted', {value:000000, auto:false,
		inc:masterModel.FBcounterInc, 
		pace:masterModel.FBcounterPace , 
		fW:masterModel.counterAllFW,  
		tFH:masterModel.counterTopFH,
		bFH:masterModel.counterBotFH,
		bOffset:masterModel.counterBottomOffset		  	
		 });

		this.mediaCounterSubmitted = new flipCounter('counter-media-submitted', {value:000000, auto:false, 
		  inc:masterModel.FBcounterInc, 
		  pace:masterModel.FBcounterPace , 
		  fW:masterModel.counterAllFW,  
		  tFH:masterModel.counterTopFH,
		  bFH:masterModel.counterBotFH,
		  bOffset:masterModel.counterBottomOffset		  	
		});
	},

	submitCounterUpdate:function(){
		var mediaValue = FbView.calMedia();
		var impressionsValue = FbView.calImpressions();

		FbView.mediaCounterSubmitted.stop();
		FbView.mediaCounterSubmitted.setValue(mediaValue);

		FbView.impressionsCounterSubmitted.stop();
		FbView.impressionsCounterSubmitted.setValue(impressionsValue);

		//resize :
		/*$("#counter-impressions-submitted").css({ 
												"left" : (960-$("#counter-impressions-submitted").width() )*.5 
											});
		
$("#counter-media-submitted-box").css({ 
												"left" : (960-$("#counter-media-submitted-box").width() )*.5 
											});
*/
	},

	sliderSetup:function(id , startValue , max , label , labelid ){
		$( "#"+id ).slider({
					value:startValue,
					animate:false,
					min: 1,
					max: max,
					//step: 1,
					slide: function( event, ui ) {
						FbView.calFriendsPostsValues(labelid, ui.value );
						FbView.updateCounters(labelid, true);//odd results when it updates too fast.. :/
						FbView.updateSliderFill(id);
					},
					stop:function(event , ui ){
						FbView.calFriendsPostsValues( labelid, ui.value )
						FbView.updateCounters(labelid, false);
						FbView.updateSliderFill(id);
					}
				});
		$( "#"+id ).data('label' , label );
		this.calFriendsPostsValues(labelid, startValue);
		this.updateCounters(labelid, true);
		this.updateSliderFill(id); 
	},

	calFriendsPostsValues:function(id, value){
		//update whichever value is being updated:
		//the value of the slider is not equal to the value displayed to users.
		if( id == 'friendsValue'){
			var weightedScore = 0;
			if( value > 98 ){
				weightedScore = value * 50
			}else if( value > 95 ){
				weightedScore = value * 40	
			}else if( value > 90 ){
				weightedScore = value * 35
			}else if( value > 85 ){
				weightedScore = value * 30	
			}else if( value > 80 ){
				weightedScore = value * 22
			}else if( value > 75 ){
				weightedScore = value * 17		
			}else if( value > 70 ){
				weightedScore = value * 15
			}else if( value < 15 ){
				weightedScore = value;
			}else{
				//range between 15 - 70
				weightedScore = value*12;
			}
			//Debug.trace(value + ' ' + weightedScore )
			value = weightedScore;
		}else{
			//convert value here before interperting it.
			value = value / FbView.postsFactorizer; //is 90 to give a smoother increment to the user.
			databaseValue = 1;
			if( value > 1 ){
				for( var f=1; f < FbView.postsValueArray.length; f++){
					if( value >= FbView.postsValueArray[f].label ){
						if( Math.abs(FbView.postsValueArray[f-1].label-value) < Math.abs(FbView.postsValueArray[f].label-value) ){
							//difference of previous value is less so it is closer to previous value
							value = FbView.postsValueArray[f-1].label;
						}else{
							value = FbView.postsValueArray[f].label;
						}
						break;
					}	
				}
			}else{
				value = 1;
				//if not values under 1 can be shown with and look bad.
			}
			//set the slide pos properly.
			var bkgdpos = '0px';
			if( value == 30 ){
				bkgdpos = '-7px';
			}else if( value == 15 || value == 10){
				bkgdpos = '-4px';
			}else if( value == 5 ){
				bkgdpos = '-1px';
			}else if( value == 1 || value == 2 || value == 3 ){
				bkgdpos = '0px';
			}
			$('#FB-slider-posts .ui-slider-handle').css({'background-position-x': bkgdpos})

			
		}
		//could be changed to an Ember 'set' option?
		FbView[id] = value;
	},

	updateCounters:function(type, animate){
		
		var mediaValue = FbView.calMedia();
		var impressionsValue = FbView.calImpressions();
		
		if( animate == true ){
			FbView.mediaCounter.incrementTo( mediaValue );
			FbView.impressionsCounter.incrementTo( impressionsValue );
		}else{
		
			Debug.trace( animate + ' media val ' + mediaValue + ' impressions val ' + impressionsValue );
			
			FbView.mediaCounter.stop();
			FbView.mediaCounter.setValue(mediaValue);
			
			FbView.impressionsCounter.stop();
			FbView.impressionsCounter.setValue(impressionsValue);	

			//snap month
			if( type == 'postsValue' ){
				//on stop/release
				Debug.trace(' -------- ' + FbView.postsValue );
				$( "#"+FbView.postsSliderID ).slider( "value" , FbView.postsValue*FbView.postsFactorizer );	
			}
					
		}
	},

	updateSliderFill:function(id){
		var label = $( "#"+id ).data("label");
		
		if( id == 'FB-slider-friends'  ){
			$( "#"+id+'-amount' ).html( FbView.friendsValue + ' ' + label );	
		}else{
			$( "#"+id+'-amount' ).html( FbView.postsValue + ' ' + label );	
		}
		
		/*
		$('#'+id + " .FB-slider-fill").clearQueue();
		$('#'+id + " .FB-slider-fill").animate({
			'width': $('#'+id + " .ui-slider-handle " ).css('left') 
		} , 200 );
		*/

		var w = $('#'+id + " .ui-slider-handle " ).css('left');
			//round it..
			//w = Math.round(w.split('%')[0]) +'%';
		$('#'+id + " .FB-slider-fill").css({
			'width': w 
		});
			
	},

	//you do not want to call the values directly from the slider as they have to be convereted properly
	//(on slide and stop the postsValue and FriendsValues are converted values from the original slider values.).
	calMedia:function(){
		var postsVal = FbView.postsValue; 	
		var friendsVal = FbView.friendsValue;	
		//postsVal needs to be converted from months to year scale.
		return Math.round( friendsVal * (postsVal*12) * .12 * .01 )
	},
	calImpressions:function(){
		var postsVal = FbView.postsValue;	
		var friendsVal = FbView.friendsValue;
		//return Math.round( friendsVal * (postsVal*12) * .10 );

		//convert posts from month to yearly.
		return Math.round( friendsVal * (postsVal*12) * .12 )	
	},
	
	//MANAGE ALL clicks of the big button regardless of state:
	////0 =  default landing/new user  //1 = submitted thank you page , //2 = logged in/returning users //3 = how it works diagram
	bigButtonClicked:function(){
		Debug.trace(' BIG BUTTON CLICKED');
		
		if( (FbView.isConnected == true) && (FbView.currentUserState == 0) ){
			//this would only occur in testing, 
			//but just in case switch current state to the proper val
			FbView.set('currentUserState', 2);
		}

		if( FbView.currentUserState == 0 ){
			//not logged in 'connect'
			FbView.fbconnect()
		}else if( FbView.currentUserState == 1 ){
			//saved 'go back'
			FbView.displaySetupState();
		}else if( FbView.currentUserState == 2){
			//returning user 'save your settings'
			FbView.updateUserInfo();
		}else if( FbView.currentUserState == 3 ){
			//infographic [hidden]
		}
	},	
	//FB CONNECTION // state change
	generateDynamicPost:function(){
		var str = "friends="+ FbView.friendsValue;

			var dbValue = 30;
			for( var p=0; p < FbView.postsValueArray.length; p++){
				if( FbView.postsValueArray[p].label == FbView.postsValue ){
					dbValue = FbView.postsValueArray[p].db;
				}
			}
			str += "&posts="+dbValue;
		if( FB.getUserID() != 0 ){
			str += "&fbid="+FB.getUserID();
		}else if( FbView.userFB != 0 ){
			str += "&fbid="+FbView.userFB;
		}else{
			str += "&fbid=0";
		}

		var issuesScore = '';
			for( var k=0; k < FbView.issuesLabels.length ; k++){
				if( FbView.issuesLabels[k].checked == true ){
					issuesScore += '1';
				}else{
					issuesScore += '0';
				}
			}
		str += "&issues="+issuesScore;
		
		//update issuesValue
		FbView.issuesValue = issuesScore;

		return str;
	},

	fbconnect:function(){
		trackEvent('FBconnect','connecting' );
		if( FbView.currentUserState == 0 ){
			var dynamicContent = "?" + FbView.generateDynamicPost();
			popupShare(masterModel.root+'/facebook/'+dynamicContent);
		}else{
			Debug.trace(' WARNING not a valid state to FB connect.');
		}
	},

	fbconnectCallback:function(result){
		Debug.trace( result );
		var numFriends = result.totalfriends;	//result.data[0].friend_count;
		
		trackEvent('FBconnect','success' );

		//update checkboxes.
		//update on:
		//		returning user login
		//		new user 
		FbView.issuesValue = result.issues_selected;
		FbView.defineRadioButtonsSelected();

		FbView.isConnected = true;
		FbView.fixFriendSlider(numFriends , false);
		FbView.displaySavedState();
	},

	//ON SAVE UPDATE USER INFO
	updateUserInfo:function(){
		FbView.displaySavedState();

		var dynamicContent = "?format=json&" + FbView.generateDynamicPost();
		//POST
		$.post(masterModel.root+'/requests/userupdate'+dynamicContent, function(data){
			Debug.trace(' update complete');
			Debug.trace(data);
		})
		Debug.trace( dynamicContent );		
	},

	donateClicked:function(){
		Debug.trace(' DONATE CLICKED ');
		trackEvent('click','donatenow' );
		window.open("http://www.videovolunteers.org/support-us");
	},

	fixFriendSlider:function( numFriends , onStart ){	
	
		var max = Math.round(numFriends*1.2);
		FbView.friendsValue = numFriends;
		
		var id = FbView.friendsSliderID;
		//need to set this here otherwise it never gets defined if user is already connected on page load.
		$( "#"+id ).data('label' , FbView.friendsLabel );

		$("#"+id).slider( "option", "max", max );
		$("#"+id).slider( "option", "value", FbView.friendsValue );
		//$( "#"+id+'-amount' ).html( $( "#"+id ).slider( "value" ) + ' ' + label );
		
		//UPDATE
		//'friendsValue', FbView.friendsValue , 
		FbView.updateCounters('friendsValue', false);
		FbView.updateSliderFill( id );
		FbView.isConnected = true;
		
		//DISABLE
		FbView.disableSlider(id);
		Debug.trace(' --- new total of friends count -- ' + FbView.friendsValue + ' MAX ' + max );
		
	},

	disableSlider:function(id){
		$("#"+id).slider("disable");
		$("#"+id).find('div').addClass('FB-slider-fill-disabled');
		$("#"+id).find('a').addClass('ui-slider-handle-disabled');
	}
	
})



var RecentView = Em.View.create({
	templateName:"vvRecent",
	headerTitle:"Recent Videos Broadcast on",
	headerCopy:"The <span class='orangeFont' >NewsFeed</span> Network",
	issues:[{name:"Education"},{name:"Woman's Rights"},{name:"Corruption"},{name:"Justice"}, {name:"Environment"}],
	
	current:{ num:-1, ytid:"", title:"", date:"", issues:"00000", 	correspondent:{	name:"", location:"", thumb:""} , sharedThumbs:[ ] },
	
	ytW:633,
	ytH:356,
	//full html generated for the carousel
	youtubeListHTML:null,
	
	//tag for iframe API
	tag:"",
	
	
	sharedbyTitle:"Other people who shared this video:",
	
	//view count call/query this: https://gdata.youtube.com/feeds/api/videos/crHfzQULoIE?alt=json ?

	recent:[],
	
	init:function(){
		this._super();
	},
	didInsertElement:function(){
		//TODO : add in JSON request here:
		Debug.trace(' LOAD list');
		if(Debug.debug == true ){
			//just takes too long to load without a cache.
			$.getJSON(masterModel.root+'/recentvideos.json', RecentView.recentListLoaded );
			//$.getJSON(masterModel.root+'/requests/recentvideos', RecentView.recentListLoaded );	
		}else{
			$.getJSON(masterModel.root+'/requests/recentvideos', RecentView.recentListLoaded );	
		}
		
		//this.recentListLoaded();
	},
	recentListLoaded:function(data){	
		//this.recent = data....
		Debug.trace( data );
		if( data.recentvideos.length > 0 ){

			//truncate string and add ellipsis
			//php script hangs
			for( var r = 0; r < data.recentvideos.length; r++){
				data.recentvideos[r].description = masterModel.truncateString( data.recentvideos[r].description , 375 );
			}

			RecentView.recent = data.recentvideos;
			
			RecentView.populateYoutubeList();
			RecentView.createCarousel();
			
			$("#recents-textPanel").css({"display":""});
			$("#recent-subscribe").click( RecentView.subscribeClicked );
			$("#recent-twitter").click( RecentView.twitterClicked );
			
			
		}
	},
	
	
	subscribeClicked:function(){
		trackEvent('click','correspondent-facebook' );
		if( typeof RecentView.current.correspondent.fb != 'undefined'){
			window.open("http://www.facebook.com/"+RecentView.current.correspondent.fb );
		}
	},

	twitterClicked:function(){
		trackEvent('click','correspondent-twitter' );
		if( typeof RecentView.current.correspondent.twitter != 'undefined'){
			window.open( "http://twitter.com/#!/"+RecentView.current.correspondent.twitter );
		}
	},
	
	populateYoutubeList:function(){
		var html = '';
		
		for( var y=0; y < RecentView.recent.length; y++){
			RecentView.recent[y].yt = Youtube.create();
			RecentView.recent[y].yt.set('width' , RecentView.ytW ); 
			RecentView.recent[y].yt.set('height' , RecentView.ytH );
			RecentView.recent[y].yt.set('embedID' , RecentView.recent[y].ytid );	
			RecentView.recent[y].yt.set('iframeID' , "carousel"+y );		
			RecentView.recent[y].num = y;
			
			html += '<div class="recents-youtubeItem" >'+RecentView.recent[y].yt.get('embed')+'</div>'
		}
		html += '<span class="clear" ></span>';
		//force a fixed width to the carousel container:
		$("#recents-carousel").css({"width": RecentView.recent.length*this.ytW });
		RecentView.set('youtubeListHTML' , html );
		RecentView.setCurrent(0);
		
		//		Debug.trace( ' --- populate list ' )
		//		Debug.trace( this.recent[0] );
		//		Debug.trace( this.current );
	},
	
	createCarousel:function(){
		if(RecentView.recent.length <= 1)$("#recent-arrow-next").addClass('disabled');
		else
		{
		$("#recent-arrow-prev").click( RecentView.clickPrev );
		$("#recent-arrow-next").click( RecentView.clickNext );
		}
		
		$("#recent-arrow-prev").addClass('disabled');
		
		
		$("#recent-gotochannel").click( RecentView.gotoChannel );
		
		
		
	},
	clickNext:function(){
		trackEvent('click','next-correspondent' );
		RecentView.interpertClick(RecentView.current.num , RecentView.current.num+1);
	},
	clickPrev:function(){
		trackEvent('click','previous-correspondent' );
		RecentView.interpertClick(RecentView.current.num , RecentView.current.num-1);
	},
	gotoChannel:function(){
		trackEvent('click','youtubechannel' );
		window.open( RecentView.current.channellink );	
	},
	interpertClick:function(prevNum , newNum){
		
		if( (newNum) <= 0 ){
			RecentView.setCurrent( 0 );
			$("#recent-arrow-prev").addClass('disabled');
			$("#recent-arrow-next").removeClass('disabled');
		}else if( newNum >= (RecentView.recent.length-1) ){
			RecentView.setCurrent( (RecentView.recent.length-1) )
			$("#recent-arrow-next").addClass('disabled');
			$("#recent-arrow-prev").removeClass('disabled');
		}else{
			RecentView.setCurrent( newNum );
			$("#recent-arrow-prev").removeClass('disabled');
			$("#recent-arrow-next").removeClass('disabled');
		}
	
		RecentView.recent[prevNum].yt.callPlayer("pauseVideo");
	
	},
	
	setCurrent:function(num){
		
		if( RecentView.current.num != num ){
			$("#recent-panel-title").css({ 'top':'10px'});
			$("#recent-panel-title").animate({'top':'0px'} , 250);
			$("#recent-panel-date").css({ 'top':'-5px'});
			$("#recent-panel-date").animate({'top':'0px' }, 250);
			$("#correspondent-box").hide().fadeIn();
			$("#recent-panel-views-count").hide().fadeIn();
			$("#recent-panel-sharedby-photos").hide().fadeIn();
			
			RecentView.set('current', RecentView.recent[ num ] );
			
			
			
			var issuesArray = RecentView.current.issues.toString().split("");
			var LiList = $('#recent-panel-issues-list').find('li');
			
			for( var i = 0; i < RecentView.issues.length ; i++){
				var _color = '#9E9E9E'; //default grey
				var li = LiList[ i ]
				if( issuesArray[i] == '1' ){
					_color = '#000000'; //active or green //#0c707d
				}
				$(li).animate({'color' : _color} , 350);
				
				
			}
			
			$("#recent-panel-description").css({'top':-15, 'opacity':0}).animate({'top':0, 'opacity':1 });

			//Debug.trace( RecentView.recent[num].correspondent.fb );
			//Debug.trace( RecentView.recent[num].correspondent.twitter );
			
			if( RecentView.recent[num].correspondent.fb ){
				$("#recent-subscribe").hide().fadeIn(200);
			}else{
				$("#recent-subscribe").hide();//fadeOut(1);
			}
			
			if( RecentView.recent[num].correspondent.twitter ){
				$("#recent-twitter").hide().fadeIn(250);
			}else{
				$("#recent-twitter").hide();//fadeOut(1);
			}
				
		}
		RecentView.animateCarouselTo();
	},
	
	animateCarouselTo:function(){
		var l = -RecentView.current.num * RecentView.ytW;
		$("#recents-carousel").animate({left:l} , 500 , 'easeInOutQuint' );
		
	}
	
	
});


