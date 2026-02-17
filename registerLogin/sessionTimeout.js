const scriptUrl = document.currentScript
    ? new URL(document.currentScript.src, window.location.href)
    : new URL('sessionTimeout.js', window.location.href);
const sessionTimeoutUrl = `${scriptUrl.origin}${scriptUrl.pathname.replace(/\/sessionTimeout\.js$/, '')}/sessionTimeout.php`;

document.addEventListener('click', function() {
    fetch(sessionTimeoutUrl, {
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


setInterval(checkSessionTimeout, 10000); 

function checkSessionTimeout() {
    fetch(sessionTimeoutUrl, {
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

