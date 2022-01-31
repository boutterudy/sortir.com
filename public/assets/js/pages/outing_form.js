window.onload = function() {
    let townSelector = document.getElementById('outing_place_town');
    let placeSelector = document.getElementById('outing_place_name');

    async function getPlaces(townId){
        const json = await fetch('/sortir.com/public/api/town/' + townId + '/places')
            .then(response => response);
        console.log(json);
        /*return await fetch('/api/town/' + townId + '/places').then((response)=>response.json())
            .then((responseJson)=>{return responseJson});*/
    }


    townSelector.addEventListener('change', async (event) => {
        let townId = townSelector.value;
        let places = await getPlaces(townId);
        console.log(places)
    });
};



