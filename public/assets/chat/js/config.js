// Domain
const domain = "http://localhost/TEMPLATE/Chat-Realtime-master/";

// MySQL API
const apis = 'assets/chat/api.php';

// set image directori
const imageDir = 'image';

// Replace with: your firebase account
const config = {
  apiKey: "Pl4MP08NEDnQh0VxLOCbigSSjD2HGYcCat85VAEh",
  authDomain: "localhost",
  databaseURL: "https://timeflown-chat.firebaseio.com",
  projectId: "timeflown-chat",
  storageBucket: "timeflown-chat.appspot.com",
  messagingSenderId: "850792714102",
  appId: "1:850792714102:web:a03636b2f52d2067f67457",
  measurementId: "G-WLYGF22TLJ"
};


firebase.initializeApp(config);
/*const config = {AIzaSyCNX1TvDwdJnm88qboxYymxsgS-im4KCVQ
    apiKey: "Pl4MP08NEDnQh0VxLOCbigSSjD2HGYcCat85VAEh",
    databaseURL: "https://timeflown-chat.firebaseio.com"
};
*/
// create firebase child
const dbRef = firebase.database().ref();

const messageRef = dbRef.child('message');
const userRef = dbRef.child('user');
