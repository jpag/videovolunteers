videovolunteers
===============

Video Volunteers / Voices


-----LOGIN / IMPORTANT INFO:
	email access
	U video.voices.webmaster@gmail.com
	P 123volunteer
	DOB Oct 18, 1982

----DEVELOPMENT / DEPLOYMENT NOTES
	in development mode all non min js files will be loaded (so you don't need to compile)
	js files are compiled with closure minify through sublime package.
	ember framework is used on frontend


-----ALGORITHM FOR IMPRESSIONS / FB MEDIA:
		can be found in Posts table.php 
			impressionsToMoneyRaised();
			impressionsMade();

		and app.js file fbview:
			calMedia();
			calImpressions();

------FRONTEND
	managed by IndexController.php
		-loads multiple header files/css/scripts determines to show MIN or NOT MIN
		-layout template.phtml
		-inserts 
			trackingscript.phtml
			scripts.phtml (all dynamic variables determined from server side are declared here for JS as well as FB connect.)
			index/index.phtml

-------CRON JOBS
	/post (run this daily, probably will be the slowest cron to run and needs the most memory/cpu)
	/youtube (run daily pulls in latest youtube videos from the predefined channel in the application.ini file)
	/monthly (run daily sends an email to users who haven't received an email yet in the past month about their stats)

------CONTROLLERS---------------

youtubeController.php
	manages all auto posting to the DB of new youtube videos looks for any new ones that are not in the database

postController.php
	manages posting to user's wall (and all that fun logic of which video to post for them, and email them if they are expiring an acces_token soon)
	ADD an FBID to the path/request to force a post on that FBID/user. (on start...)

monthlyController.php
	cron job to send a monthly email to users 
	looks from the DB of users that haven't recieved an email in a month or more and sends a custom email about their stats
	made more sense to remove this component from the POST command (as it just created more strain).

AdminController.php
	all cms views are managed here	

FacebookController.php
	all Facebook components (connection, and renewing access token calls)

RequestsController.php
	a general requests from front end scripts, manages anything that the js may call for in JSON.
		/userstats specific stats for each individual from their FBID
		/userupdate json post/write user changes to the DB
		/recentvideos gets recent videos

IndexController.php
	manages main page view



---------TODOS
	cron test (push see how much it can do on media temple's server)s

	add a no script warning (turn on your JS)

	email styling:
	http://collab.stinkdigital.com/attachments/4536?disposition=inline&project_id=148
	email through facebook's "SEND" feature - via smtp

	adjust the overall score confirm it is adding properly.

	//added monthly email feature in the POST controller
	//add tracking
	//created a way to concate a paragraph description on the request/recentvideos call
	//created new table video call for recent videos by themselves for /POST call (does not need the previous post info)
	//youtube description and link and view count added to recent video list in query call. in video table.php
	//re styling css and html.
	//cache unique to the environment (staging or production)
	//user stats request with id.
	//pull only active videos
	//notify in cms when video is not active
	//create youtube auto pull script
	//create staging
	//activate staging.. crons
