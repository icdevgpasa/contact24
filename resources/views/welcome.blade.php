<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript">
    (function(d, m){
        var kommunicateSettings = 
            {"appId":"1ee8820b33511e8aedac500af364d3fae","popupWidget":true,"automaticChatOpenOnNavigation":true,"conversationTitle":"No tutaj jakis tytul"
,
 "onInit": function() {
KommunicateGlobal.document.querySelector('#mck-conversation-title').style.display="none";
KommunicateGlobal.document.querySelector(".mck-back-btn-container").style.display="none";
KommunicateGlobal.document.querySelector("#mck-attachfile-box").style.display="none";
KommunicateGlobal.document.querySelector('#mck-text-box').setAttribute('data-text','');
    },};
        var s = document.createElement("script"); s.type = "text/javascript"; s.async = true;
        s.src = "https://widget.kommunicate.io/v2/kommunicate.app";
        var h = document.getElementsByTagName("head")[0]; h.appendChild(s);
        window.kommunicate = m; m._globals = kommunicateSettings;
    })(document, window.kommunicate || {});
/* NOTE : Use web server to view HTML files as real-time update will not work if you directly open the HTML file in the browser. */
</script>
        <title>Chat'N'Roll</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
.mck-running-on {
display:none;
}
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body style='background-color:#525252;'>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md" style='color:white; font-size:32px;'>
			<img style='height:100px; margin-right:20px' src='/logo.png'/>
			<img style='margin-left:20px;' src='https://hackyeah.pl/wp-content/uploads/2020/09/logo-z-govtechem_biale-04versionSMALL.png'/>
			<br/><br/>
			ChatBot & VoiceBot<br/><br/>WebPage (bottom right corner)<br/><br/>Facebook (Almost ready)<br/><br/>Phone - just dial <b>+48 22 266 28 53</b>
                </div>

	
            </div>
        </div>
    </body>
</html>
