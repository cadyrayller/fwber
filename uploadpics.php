<?php
session_start();

include("_names.php");
include("_debug.php");

?>
<!doctype html>
<html lang="en">
<head>
    <title><?php echo getSiteName(); ?> - Upload Pictures<?php echo getTitleTagline(); ?></title>
	<?php include("head.php");?>
</head>
<body class="d-flex flex-column h-100">
	<?php include("h.php");?>
    <div class="col-sm-12 my-auto text-center">
        <br>
        <br>
        <br>
        <br>

        <h1 class="h3 mb-3 font-weight-normal text-center"> Manage Pictures</h1>
        <br>
        <br>


        <div class="card mb-5 shadow-sm" style="display:inline-block; background:#ddddff;">
            <br>
            <br>
            <h1 class="h3 mb-3 font-weight-bold text-center">Your Public Pictures</h1>
            <div class="smallText">
                These are public and shown to anyone who matches with you.<br>
            </div>
            <br>
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <div id="picsFirstBase">
                        </div>
<?php

    include("_debug.php");
    include("_secrets.php");

		//connect to database, find any firstbase pictures for me.
		$db = mysqli_connect($dburl,$dbuser,$dbpass);
		if(!$db)exit(mysqli_connect_error());

		$email = mysqli_escape_string($db,$_SESSION["email"]);	
	
		//get my userid
		$dbquerystring = sprintf("SELECT id, firstBasePics, allTheWayPics FROM ".$dbname.".users WHERE email='%s'",$email);
		$dbquery = mysqli_query($db,$dbquerystring);
		$dbresults = mysqli_fetch_array($dbquery);
		
		$userid=$dbresults['id'];
		
		$firstBasePics=explode(",",trim(trim($dbresults['firstBasePics']),","));
		$allTheWayPics=explode(",",trim(trim($dbresults['allTheWayPics']),","));
		
		mysqli_free_result($dbquery);
		
		//output them here with a delete button, with a php get image script.
		foreach($firstBasePics as $s)
		{
			if($s=="")continue;
?>
            <div class="col-md-5" id="imgdiv<?php echo $s; ?>">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3" align="center" width="100%" height="400">
                            <a href="/_getImage?img=<?php echo $s; ?>&id=<?php echo $userid; ?>" target="_blank"><img src="/_getImage?img=<?php echo $s; ?>&id=<?php echo $userid; ?>" id="pic<?php echo $s; ?>" style="width:90%;"></a>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" name="delete" id="deleteButton<?php echo $s; ?>" onclick="ajaxDelete('<?php echo $s; ?>');">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php 
		}
?>
            </div>
        </div>
            <div class="ml-5 mr-5">
                <form action="_imageUpload" method="post" name="firstBaseForm" id="firstBaseForm" enctype="multipart/form-data">
                    <label>Upload Image: <input type="file" name="filedata" id="filedata" onchange="ajaxUpload(this.form,'firstBase');return false;"/></label>
                </form>
                <span style="font-size:8pt;font-style:italic;">
                File Types: .jpg, .png, 50MB maximum.
                </span>
            </div>
        </div>
    </div>

<br>
<br>
<div class="card mb-5 shadow-sm" style="display:inline-block; background:#ffdddd;">
    <br>
    <br>
    <h1 class="h3 mb-3 font-weight-bold text-center">Your Private Pictures</h1>
    <div class="smallText">
        Only members you have authorized will see these pictures.<br>
    </div>
    <br>
    <div class="album py-5 bg-light">
        <div class="container">
            <div class="row">
                <div id="picsAllTheWay">
                </div>
<?php
		//output them here with a delete button, with a php get image script.
		foreach($allTheWayPics as $s)
		{
			if($s=="")continue;
?>
            <div class="col-md-5" id="imgdiv<?php echo $s; ?>">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3" align="center" width="100%" height="400">
                            <a href="/_getImage?img=<?php echo $s; ?>&id=<?php echo $userid; ?>" target="_blank"><img src="/_getImage?img=<?php echo $s; ?>&id=<?php echo $userid; ?>" id="pic<?php echo $s; ?>" style="width:90%;"></a>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" name="delete" id="deleteButton<?php echo $s; ?>" onclick="ajaxDelete('<?php echo $s; ?>');">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php
		}
		//handle delete button.
?>
                    </div>
                </div>
                <div class="ml-5 mr-5">
                    <form action="_imageUpload" method="post" name="allTheWayForm" id="allTheWayForm" enctype="multipart/form-data">
                        <label>Upload Image: <input type="file" name="filedata" id="filedata" onchange="ajaxUpload(this.form,'allTheWay');return false;"/></label>
                    </form>
                    <span style="font-size:8pt;font-style:italic;">
                        File Types: .jpg, .png, 50MB maximum.
                        </span>
                </div>
            </div>
        </div>
    </div>
    <?php include("f.php");?>

    <script type="text/javascript" src="js/ajaxupload.js"></script>

    <style type="text/css">
        iframe #ajax-temp
        {
            display:none;
        }

        img
        {
            border: 0px;
        }
        #upload_area
        {
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
    </style>

    <script src="/js/jquery/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="/js/jquery-ui-1.8.21.custom.min.js" type="text/javascript"></script>

    <script type="text/javascript">

        function ajaxDelete(imgID)
        {
            var e = document.getElementById("deleteButton"+imgID);

            $(e).hide("explode","",200);

            $.ajax(
                {
                    type: "GET",
                    url: "_deleteImage",
                    data: "img=" + escape(imgID),
                    dataType: "html",
                    success: function(text)
                    {
                        if(text=="done")
                        {
                            $(document.getElementById("imgdiv"+imgID)).hide("explode","",1000);
                        }
                        else
                        {
                            $(document.getElementById("img"+imgID)).hide("puff","",1000);
                            document.getElementById("imgdiv"+imgID).innerHTML="Something went wrong. Please try again later.<br>Error: " + text;
                        }
                    }
                });
        }
    </script>
</body>
</html>