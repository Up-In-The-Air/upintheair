var source = new EventSource("api/message_controller.php");

if(typeof(EventSource) !== "undefined") {
    source.onmessage = function(event) {
        document.getElementById("result").innerHTML += JSON.parse(event.data).time + "<br>";
    };
} else {
    document.getElementById("result").innerHTML = "Sorry, your browser does not support server-sent events...";
}
