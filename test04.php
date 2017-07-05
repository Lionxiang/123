<!DOCTYPE html>
<html>
<?php
include_once 'dbconfig.php';
$db_link = mysqli_connect("120.119.164.164:8082", "root", "123", "db1031241216") or die("無法建立連接");
mysqli_query($db_link,"SET CHARACTER SET UTF8");
session_start();
?>	
<div id="message"></div>
<head>
<title>藏寶圖</title>
<meta name="viewport" content="initial-scale=1.0">
<meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
	    overflow-y: scroll;
　      overflow-x: hidden;
      }
      #map {
        height: 50%;
      }
	  
    </style>
</head>

<body onload="loadDemo()"> 
	<form method="POST" name="form1" id="form1" action="updateCoin.php"> 
		  <input type="hidden" name="tId" value="t01">
		  <button type="submit" onclick="update(tId)" class="button">updateCoinFortarea</button>
	</form>
	<div id="map"></div>	
	
	<script type="text/javascript">     //計算距離和取得經緯度的function
	    //var latitude = position.coords.latitude;
        //var longitude = position.coords.longitude;
        //var accuracy = position.coords.accuracy;
	
    var totalDistance = 0.0;
    var lastLat;
    var lastLong;

    Number.prototype.toRadians = function() {       //傳回距離值(與眾不同-待補參考網址)
      return this * Math.PI / 180;
    }
	
    function distance(latitude1, longitude1, latitude2, longitude2) {
      // R是地球半徑（KM）
      var R = 6371;

      var deltaLatitude = (latitude2-latitude1).toRadians();
      var deltaLongitude = (longitude2-longitude1).toRadians();
      latitude1 = latitude1.toRadians();
	  latitude2 = latitude2.toRadians();

      var a = Math.sin(deltaLatitude/2) *
              Math.sin(deltaLatitude/2) +
              Math.cos(latitude1) *
              Math.cos(latitude2) *
              Math.sin(deltaLongitude/2) *
              Math.sin(deltaLongitude/2);

      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      var d = R * c;
      return d;
    }


    function updateStatus(message) {
        document.getElementById("status").innerHTML = message;
    }
    
     setInterval(function loadDemo() {
	  console.log('1');
        if(navigator.geolocation) {
            updateStatus("瀏覽器支持HTML5 Geolocation。");
            navigator.geolocation.watchPosition(updateLocation, handleLocationError, {maximumAge:1000});
        }
    },1000);

    function updateLocation(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        var accuracy = position.coords.accuracy;

        document.getElementById("latitude").innerHTML = latitude;
        document.getElementById("longitude").innerHTML = longitude;
        document.getElementById("accuracy").innerHTML = accuracy;
    console.log(latitude,longitude,accuracy);
        // 如果accuracy的值太大，我們認為他不準確，不用它計算距離
        if (accuracy >= 5000) {
            updateStatus("這個數據不太準確，需要更準確的數據來計算本次移動距離。");
            return;
        }

        // 計算移动距离

        if ((lastLat != null) && (lastLong != null)) {
            var currentDistance = distance(latitude, longitude, lastLat, lastLong);
            document.getElementById("currDist").innerHTML =
              "本次移動距離：" + currentDistance.toFixed(4) + " 公里";

            totalDistance = totalDistance + currentDistance;

            document.getElementById("totalDist").innerHTML =
              "總計移動距離：" + totalDistance.toFixed(4) + " 公里";
        }

        lastLat = latitude;
        lastLong = longitude;
        updateStatus("計算移動距離成功。");
	
    }
    function handleLocationError(error) {
        switch(error.code)
        {
        case 0:
          updateStatus("嘗試或取您的位置信息時發生錯誤：" + error.message);
          break;
        case 1:
          updateStatus("用戶拒絕了獲取位置信息請求。");
          break;
        case 2:
          updateStatus("瀏覽器無法獲取您的位置信息：" + error.message);
          break;
        case 3:
          updateStatus("獲取您位置信息超時。");
          break;
        }
    }	
    </script>
	
	<script>
	var x=document.getElementById("message");

    var coordinates = [
    [lastLat, lastLong],
    ];

	</script>
	
    <script>

        function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 22.7259888, lng: 120.3127749},            //進入網頁時的中心點
          zoom: 19
        });
        var infoWindow = new google.maps.InfoWindow({map: map});
        // Try HTML5 geolocation.
        if (navigator.geolocation) {
          navigator.geolocation.watchPosition(function(position) {
            var pos = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            infoWindow.setPosition(pos);
            infoWindow.setContent('Hello~小小探險家這是你現在的位置');
            map.setCenter(pos);	
			//使用者marker icon
			//頭                             //有BUG(必須將old marker移除-目前想到:for迴圈0~1)
			setInterval(function(){
				//alert("3");
				marker.setMap(null);
			},3000);

			var marker = new google.maps.Marker({ 		
            position: pos,
			draggable:true,
			icon : "https://lionxiang.github.io/Treasure/explorer4.png",
            map: map
            });			
	        var contentString = "<h1>目前位置</h1><p><img src='https://lionxiang.github.io/Treasure/explorer4.png' style='float: left;'>我叫維剛，我要成為冒險王!!</p>";
  	        var infowindow = new google.maps.InfoWindow({
    	    content: contentString,
		    maxWidth: 210
	        });	
	        marker.addListener('click', function() {
		    infowindow.open(map, marker);
	        });			
			//尾
			//寶藏1-START
			var pos2 ={lat: 22.7251811 , lng: 120.3145459};            
	        var marker2 = new google.maps.Marker({
            icon : "https://lionxiang.github.io/Treasure/treasure.png",
            position: pos2,
            map: map
            });
	        var contentString2 = "<h1>寶藏位置提示</h1><p><img src='https://lionxiang.github.io/Treasure/prompt1.jpg' style='float: left;'>外觀似廟又非廟，專題讓我哇哇叫，資管數學有大刀，維剛讀到想要逃。</p>";
  	        var infowindow2 = new google.maps.InfoWindow({
    	    content: contentString2,
		    maxWidth: 210
	        });	
	        marker2.addListener('click', function() {
		    infowindow2.open(map, marker2);
	        });
			//寶藏1-END
          }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
          });
        } else {
          // Browser doesn't support Geolocation
          handleLocationError(false, infoWindow, map.getCenter());
        }
      }
	  
      function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');
      }
    </script> 
<script>
	
	
    function update(tId){
	window.event.returnValue=false;  //停止送出表單
	swal({
	  title:"尋獲寶藏，取得金幣。",
	  text:"",
	  showCancelButton: false,
	  closeOnConfirm: false
	},
	function(){
	  document.cookie="tId"+tId;
	  document.getElementById("form1").submit();
	});
  }
	</script>
    <p id="status">該瀏覽器不支持HTML5 Geolocation。</p>
    <h2>當前位置：</h2>
    <table border="1">
    <tr>
    <td width="40" scope="col">緯度</th>
    <td width="114" id="latitude">?</td>
    </tr>
    <tr>
    <td>經度</td>
    <td id="longitude">?</td>
    </tr>
    <tr>
    <td>準確度</td>
    <td id="accuracy">?</td>
    </tr>
    </table>
    <h4 id="currDist">本次移動距離：0 公里</h4>
    <h4 id="totalDist">總計移動距離：0 公里</h4>
    
    <script async defer
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtBb2wMesjlWbJBngogK4otsCXOXlr7Kk&callback=initMap">
	</script>
</body>
</html>
