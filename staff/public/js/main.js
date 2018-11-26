var game = "";
    gameUrl = "";

var hashes = window.location.href.split('/');

if (
        hashes[3] == "dota2" || 
        hashes[3] == "csgo" || 
        hashes[3] == "lol" || 
        hashes[3] == "hots" || 
        hashes[3] == "overwatch"
    ) {
    game = hashes[3];
    gameUrl = "/" + hashes[3];
}