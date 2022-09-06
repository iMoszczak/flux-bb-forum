/*
    Copyright itsnuub (GameSense) 2021, all rights reserved.
    The materials contained in this website are protected by applicable copyright and trademark law.
*/




function getData() {
        $.ajax({
            type: 'POST',
            url: 'chat.php',
            success: function(respText) {
                 $('#chatmessages').html(respText);
            }
        })

}

function handleKeyPress(event) {
        if (event.keyCode == 13 && !event.shiftKey) {
            sendMessage();
            try {
                event.preventDefault();
            } catch (e) {
                event.returnValue = false; // IE
            }
            return false;
        }
        return true;
    }

function sendMessage() {
    var message = $("#shouttext").val();
    if (message.length == 0) {
        return;
    }

    if (message) {
        let request = new XMLHttpRequest();
        request.open("POST", 'chat.php', true);
        request.send(JSON.stringify({
			text: $('#shouttext').val(),
			action: 1,
			csrf: $('meta[name="csrf-token"]').attr('content')
		}));
        $.ajax({
            type: 'POST',
            url: 'chat.php',
            success: function(respText) {
                 $('#chatmessages').html(respText);
                 var shout = document.getElementById('shout');
                 var chats = shout.children[0];
                 document.getElementById('shouttext').value = "";
                 setTimeout(() => {  chats.scrollTop = chats.scrollHeight; }, 500);
                 
            }
        });

    }
}

$(function() {
    getData();
    var shout = document.getElementById('shout');
    var chats = shout.children[0];
    chats.scrollTop = chats.scrollHeight;
    setInterval(function() {
        getData();
    }, 1000);
    

});
