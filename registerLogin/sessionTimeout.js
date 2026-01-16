document.addEventListener('click', function() {
    fetch('/Makina/registerLogin/sessionTimeout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({updateActivity: true})
    })
    .then(response => response.json()) 
    .then(result => {
        if(result.active){
            console.log("Sessioni eshte aktiv");
        }
    })
    .catch(error => {
        console.error('Timeout Error:', error);
    });

    
});


setInterval(checkSessionTimeout, 10000); // Kontrollo çdo 10 sekond

function checkSessionTimeout() {
    fetch('/Makina/registerLogin/sessionTimeout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({updateActivity: false}) 
    })
    .then(response => response.json()) 
    .then(result => {
        if(result.redirect){
            window.location.href = "login.html";
        }
    })
    .catch(error => {
        console.error('Timeout Error:', error);
    });
}

