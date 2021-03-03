(function() {

    console.log('checkSystemRequirements');
    console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));



    // it's option if you want to change the WebSDK dependency link resources. setZoomJSLib must be run at first
    // if (!china) ZoomMtg.setZoomJSLib('https://source.zoom.us/1.7.8/lib', '/av'); // CDN version default
    // else ZoomMtg.setZoomJSLib('https://jssdk.zoomus.cn/1.7.8/lib', '/av'); // china cdn option 
    // ZoomMtg.setZoomJSLib('http://localhost:9999/node_modules/@zoomus/websdk/dist/lib', '/av'); // Local version default, Angular Project change to use cdn version
    ZoomMtg.preLoadWasm();
    ZoomMtg.prepareJssdk();

    var API_KEY = 'Whc2DGTrQgSO-Oj4aS2yvA';

    /**
     * NEVER PUT YOUR ACTUAL API SECRET IN CLIENT SIDE CODE, THIS IS JUST FOR QUICK PROTOTYPING
     * The below generateSignature should be done server side as not to expose your api secret in public
     * You can find an eaxmple in here: https://marketplace.zoom.us/docs/sdk/native-sdks/web/essential/signature
     */
    var API_SECRET = 'qFhDPhdFHKHtvwIsy1x9MZ5EuYVsVKBxwoH0';

    testTool = window.testTool;
    //var display_name = "Conférencier";
    var display_name = document.getElementById('display_name').value;
    var meeting_number = "7986510623";
    var meeting_pwd = "ajR1T2wxMkUyYWRzTy9QZ3pRRGRUdz09";
    var meeting_lang = 'fr-FR';
    //var meeting_role = 1;
    var meeting_role = document.getElementById('meeting_role').value;

    if (testTool.getCookie("meeting_lang")) document.getElementById('meeting_lang').value = testTool.getCookie("meeting_lang");

    document.getElementById('meeting_lang').addEventListener('change', function(e) {
        testTool.setCookie("meeting_lang", document.getElementById('meeting_lang').value);
        $.i18n.reload(document.getElementById('meeting_lang').value);
        ZoomMtg.reRender({ lang: document.getElementById('meeting_lang').value });
    });

    document.getElementById('clear_all').addEventListener('click', function(e) {
        testTool.deleteAllCookies();
        document.getElementById('display_name').value = '';
        document.getElementById('meeting_number').value = '';
        document.getElementById('meeting_pwd').value = '';
        document.getElementById('meeting_lang').value = 'en-US';
        document.getElementById('meeting_role').value = 0;
    });

    document.getElementById('join_meeting').addEventListener('click', function(e) {

        e.preventDefault();
        //var display_name = "Conférencier";
        display_name = document.getElementById('display_name').value;
        meeting_number = "7986510623";
        meeting_pwd = "ajR1T2wxMkUyYWRzTy9QZ3pRRGRUdz09";
        meeting_lang = 'fr-FR';
        //var meeting_role = 1;
        meeting_role = document.getElementById('meeting_role').value;


        if (!this.form.checkValidity()) {
            alert("Enter Name and Meeting Number");
            return false;
        }

        var meetConfig = {
            apiKey: API_KEY,
            apiSecret: API_SECRET,
            meetingNumber: parseInt(meeting_number),
            userName: display_name,
            passWord: meeting_pwd,
            leaveUrl: "https://" + window.location.hostname,
            role: parseInt(meeting_role, 10)
        };
        testTool.setCookie("meeting_number", meetConfig.meetingNumber);
        testTool.setCookie("meeting_pwd", meetConfig.passWord);


        var signature = ZoomMtg.generateSignature({
            meetingNumber: meetConfig.meetingNumber,
            apiKey: meetConfig.apiKey,
            apiSecret: meetConfig.apiSecret,
            role: meetConfig.role,
            success: function(res) {
                console.log(res.result);
            }
        });

        ZoomMtg.init({
            leaveUrl: "https://" + window.location.hostname,
            success: function() {
                ZoomMtg.join({
                    meetingNumber: meetConfig.meetingNumber,
                    userName: meetConfig.userName,
                    signature: signature,
                    apiKey: meetConfig.apiKey,
                    passWord: meetConfig.passWord,
                    success: function(res) {
                        $('#nav-tool').hide();
                        console.log('join meeting success');
                    },
                    error: function(res) {
                        console.log(res);
                    }
                });
            },
            error: function(res) {
                console.log(res);
            }
        });

    });

    function btnclick() {
        document.getElementById('join_meeting').click();

    }

})();