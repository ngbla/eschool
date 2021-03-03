// Domain
//const domain = "http://localhost/TEMPLATE/Chat-Realtime-master/";
const domain = "http://" + window.location.hostname + "/public/chat-admin/";
// MySQL API
const apis = 'api.php';
// set image directori
const imageDir = 'image';
// Replace with: your firebase account
var config = {
    apiKey: "AIzaSyChklMgH6kX_-1WhlXH6wvtO5yeTbH8m7M",
    authDomain: "aibs-53a6c.firebaseapp.com",
    databaseURL: "https://aibs-53a6c.firebaseio.com",
    projectId: "aibs-53a6c",
    storageBucket: "aibs-53a6c.appspot.com",
    messagingSenderId: "155722236731",
    appId: "1:155722236731:web:f20a17a407134b7e736d1e",
    measurementId: "G-ZNY50H5TB1"
};


firebase.initializeApp(config);


// create firebase child
const dbRef = firebase.database().ref();
//chat_realtime == users :message_admin_users     ----- messages = messages_admin
const messageRef = dbRef.child('messages_admin');
const userRef = dbRef.child('message_admin_users');