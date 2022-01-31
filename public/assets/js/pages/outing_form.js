window.onload = function() {
    let townSelector = document.getElementById('outing_place_town');
    let placeSelector = document.getElementById('outing_place_name');

    async function getPlaces(townId){
        const json = await fetch('/sortir.com/public/api/town/' + townId + '/places')
            .then(response => response.json());

        return json;
    }

    async function getPlaceInfo(placeId){
        const json = await fetch('/sortir.com/public/api/place/' + placeId)
            .then(response => response.json());

        return json;
    }


    townSelector.addEventListener('change', async (event) => {
        let townId = townSelector.value;
        let places_json = await getPlaces(townId);

        let L = placeSelector.options.length - 1;
        for(let i = L; i >= 1; i--) {
            placeSelector.remove(i);
        }

        for(let i in places_json)
        {
            let opt = document.createElement("option");
            opt.value = places_json[i].id;
            opt.innerHTML = places_json[i].name; // whatever property it has

            // then append it to the select element
            placeSelector.appendChild(opt);
        }
    });

    placeSelector.addEventListener('change', async (event) => {
        let streetField = document.getElementById('outing_place_street');
        let postalCodeField = document.getElementById('outing_place_town_postal_code');
        let latitudeField = document.getElementById('outing_place_latitude');
        let longitudeField = document.getElementById('outing_place_longitude');

        let place_json = await getPlaceInfo(placeSelector.value);

        streetField.value = place_json[0].street;
        postalCodeField.value = place_json[0].postal_code;
        latitudeField.value = place_json[0].latitude;
        longitudeField.value = place_json[0].longitude;

    });
};

