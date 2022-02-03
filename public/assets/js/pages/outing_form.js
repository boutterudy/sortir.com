window.onload = function() {
    let townSelector = document.getElementById('outing_town');
    let placeSelector = document.getElementById('outing_place');


    async function placeSelectorLoad(){
        let streetField = document.getElementById('outing_street');
        let postalCodeField = document.getElementById('outing_postal_code');
        let latitudeField = document.getElementById('outing_latitude');
        let longitudeField = document.getElementById('outing_longitude');

        let place_json = await getPlaceInfo(placeSelector.value);

        streetField.value = place_json[0].street;
        postalCodeField.value = place_json[0].postal_code;
        latitudeField.value = place_json[0].latitude;
        longitudeField.value = place_json[0].longitude;
    }

    placeSelectorLoad().then(r => true);

    townSelector.addEventListener('change', async (event) => {
        let townId = townSelector.value;
        console.log('coucou');
        let places_json = await getPlaces(townId);

        console.log(places_json)

        let L = placeSelector.options.length - 1;
        for(let i = L; i >= 1; i--) {
            placeSelector.remove(i);
        }

        for(let i in places_json)
        {
            let opt = document.createElement("option");
            opt.value = places_json[i].id;
            opt.innerHTML = places_json[i].name;

            // then append it to the select element
            placeSelector.appendChild(opt);
        }
    });

    placeSelector.addEventListener('change', async (event) => {
        await placeSelectorLoad();
    });
};

