window.onload = function() {
    
    var howfast_client_end = window.performance.now() || Date.now();
    var howfast_client_elapsed = (howfast_client_end - howfast_client_start) / 1000;
    var howfast_client_elapsed_styled = Number.parseFloat(howfast_client_elapsed).toFixed(5);
    var howfast_display = document.getElementById('howfast_display');

    if (howfast_display) {
        howfast_display.innerHTML = 'Server completed all tasks in ' + howfast_server_elapsed + ' seconds. Page loaded on client in ' + howfast_client_elapsed_styled + ' seconds.';
        howfast_display.classList.add('ready');
    }

}