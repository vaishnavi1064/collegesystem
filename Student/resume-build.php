<!---------------- Session starts form here ----------------------->
<?php  
	session_start();
	if (!$_SESSION["LoginStudent"])
	{
		header('location:../login/login.php');
	}
	if($_SESSION['LoginStudent']){
		$_SESSION['LoginAdmin'] = "";
	}
		require_once "../connection/connection.php";
		
	?>
<!---------------- Session Ends form here ------------------------>


<!doctype html>
<html>

<head>
  <title>Chat App</title>
  <?php include('../common/common-header.php') ?>  
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="{% static 'styles/style.css' %}">
  <script src="https://cdn.jsdelivr.net/npm/highlight.js@10.7.2/lib/languages/python.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/a11y-dark.min.css">
     <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
</head>

<body>
  <div class="container mt-5">
    <h1 style="color:white">Medical Assistance AI Chat Bot</h1>
    <div class="chat-box mt-3">
      <!-- Chat messages will be added here dynamically -->
      <div class="mt-3 p-3 rounded bot-message"><img src="/static/images/gpt.png" class="bot-icon"><p>Welcome, I am your personal medical assitance AI chat bot. How may I help you?</p></div>
      
    </div>
    <div class="form-group mt-3">
      <textarea class="form-control" rows="3" placeholder="Type your message here" id="message-input"></textarea>
    </div>
    <button type="button" class="btn btn-primary" id="send-btn">Send</button>
  </div>
  <script>
setInterval(highlightAll,1000);
// Function to highlight code using highlight.js library
function highlightAll() {
  document.querySelectorAll("pre code").forEach(block => { 
    hljs.highlightBlock(block);
  });
}

    const chatBox = document.querySelector(".chat-box");
    const messageInput = document.querySelector("#message-input");
    const sendBtn = document.querySelector("#send-btn");

    function addMessage(message, isUserMessage) {
      const messageDiv = document.createElement("div");
      messageDiv.classList.add("mt-3", "p-3", "rounded");

      if (isUserMessage) {
        messageDiv.classList.add("user-message");

      } else {
        messageDiv.classList.add("bot-message");
      }

      messageDiv.innerHTML = `
        <img src="{% static 'images/user.png' %}" class="user-icon"><p>${message}</p>
        `;

      chatBox.appendChild(messageDiv);
      chatBox.scrollTop = chatBox.scrollHeight;
    }




    
    let sendMessage = async () => {
      const message = messageInput.value.trim();
      if (message !== "") {
        addMessage(message, true);
        messageInput.value = ""
        let gptresponse = async () => {
            let response = await fetch('/api/', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json'
                },
                body:JSON.stringify({'message':message})
            })

                let res = await response.json()
                return res
        }

        const con = await gptresponse();
        console.log(con);
        console.log("message received");
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("mt-3", "p-3", "rounded");
        messageDiv.classList.add("bot-message");
        // Check if the content has code block
        const hasCodeBlock = con.content.includes("```");
        if (hasCodeBlock) {
          // If the content has code block, wrap it in a <pre><code> element
          const codeContent = con.content.replace(/```([\s\S]+?)```/g, '</p><pre><code>$1</code></pre><p>');             
          messageDiv.innerHTML = `<img src="{% static 'images/gpt.png' %}" class="bot-icon"><p>${codeContent}</p>`          
        }
        else{
          messageDiv.innerHTML = `<img src="{% static 'images/gpt.png' %}" class="bot-icon"><p>${con.content}</p>`
        }
        chatBox.appendChild(messageDiv);
        console.log(messageDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
      }   
    }

    
    sendBtn.addEventListener("click", sendMessage);
    messageInput.addEventListener("keydown", event => {
      if (event.keyCode === 13 && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
      }
    });
  </script>
</body>

</html>