<!DOCTYPE html>
<html>
<head>
    <title>Conférence en ligne</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var videoStream; // Variable to store the video stream

            $('#startConference').click(function() {
                // Autoriser la caméra et le microphone
                navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                    .then(function(stream) {
                        // Afficher la vidéo dans un élément HTML avec l'id "videoElement"
                        var videoElement = document.getElementById('videoElement');
                        videoElement.srcObject = stream;
                        videoStream = stream; // Store the video stream for later use
                    })
                    .catch(function(error) {
                        console.log("Une erreur s'est produite : " + error);
                    });
            });

            $('#stopConference').click(function() {
                // Arrêter la vidéo et le son
                var videoElement = document.getElementById('videoElement');
                videoElement.pause();
                videoElement.srcObject = null;

                // Arrêter le flux vidéo
                if (videoStream) {
                    var tracks = videoStream.getTracks();
                    tracks.forEach(function(track) {
                        track.stop();
                    });
                }
            });
        });
    </script>
    <style>
        /* CSS styles here */
        * {
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            box-sizing: border-box;
        }

        .cont {
            width: 100%;
            height: 100vh;
            background-position: center;
            background-size: cover;
            position: relative;
        }

        .form-box {
            width: 90%;
            max-width: 450px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 50px 60px 70px;
            text-align: center;
        }

        /* Add your CSS styles here */

    </style>
</head>
<body>
    <h1>Conférence en ligne</h1>
    <div class="cont">
        <div class="form-box">
            <h1>Conférence en ligne</h1>
            <video id="videoElement" width="640" height="480" autoplay></video>
            <br><br>
            <button id="startConference">Démarrer la conférence</button>
            <button id="stopConference">Arrêter la conférence</button>
        </div>
    </div>
</body>
</html>