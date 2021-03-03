// Domain
const domain = "http://" + window.location.hostname + "/public/classe_chat/";

// MySQL API
const apis = 'api.php';

// set image directori
const imageDir = 'image';

// Replace with: your firebase account
const config = {
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
//firebase.analytics();
/*const config = {AIzaSyCNX1TvDwdJnm88qboxYymxsgS-im4KCVQ
    apiKey: "Pl4MP08NEDnQh0VxLOCbigSSjD2HGYcCat85VAEh",
    databaseURL: "https://timeflown-chat.firebaseio.com"
};
*/
// create firebase child
const dbRef = firebase.database().ref();

const messageRef = dbRef.child('messages_admin');
const userRef = dbRef.child('message_admin_users');