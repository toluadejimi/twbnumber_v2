
    function updateLinks() {
        var selectElement = document.getElementById('countries');
        var selectedValue = selectElement.value;
        var links = document.querySelectorAll('[id^="countryLink_"]');
        links.forEach(function(link) {
            var serviceName = link.querySelector('span:first-child').textContent;
            var price = link.querySelector('span:nth-child(2)').textContent;
            link.href = "/order-sim?service=" + serviceName + "&country=" + selectedValue + "&cost=" + price;
        });
    }


    function getSelectedCountry() {
        var selectElement = document.getElementById('countries');
        var selectedValue = selectElement.value;
        console.log("Selected Country Code:", selectedValue);

        // Send a request to the API endpoint
        fetch('https://onlinesim.io/api/getNumbersStats.php?country=' + selectedValue)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); // assuming response is JSON
            })
            .then(data => {
                // Handle the data returned from the API
                console.log('Data received:', data);
                updateServices(data.services, selectedValue);
                // You can update your UI or perform further actions with the data
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                // Handle errors here
            });
    }

    function updateServices(services, country) {
        var servicesList = document.getElementById('servicesList');
        // Clear existing services
        servicesList.innerHTML = '';

        // Check if services is an array or an object
        if (Array.isArray(services)) {
            // If services is an array, iterate over each service
            services.forEach(function(service) {
                addServiceElement(service, country);
            });
        } else if (typeof services === 'object') {
            // If services is an object, iterate over its keys
            Object.keys(services).forEach(function(key) {
                addServiceElement(services[key], country);
            });
        }
    }

    function addServiceElement(service, country) {
        var servicesList = document.getElementById('servicesList');
        var a = document.createElement('a');
        a.className = "flex justify-between hover:text-white service-item hover:bg-blue-500 hover:p-2 hover:rounded-md";
        a.href = `/order-sim?service=${service.service}&price=${service.price}&country=${country}`;
        var span1 = document.createElement('span');
        span1.textContent = service.service;
        var span2 = document.createElement('span');
        var price = {{ $get_rate3 }} * service.price + {{ $margin3 }};
        span2.textContent = `N ${price.toFixed(2)}`;
        a.appendChild(span1);
        a.appendChild(span2);
        servicesList.appendChild(a);
    }


    function searchServices() {
        var input = document.getElementById('services');
        var filter = input.value.toUpperCase();
        var services = document.getElementsByClassName('service-item');

        for (var i = 0; i < services.length; i++) {
            var service = services[i].getElementsByTagName('span')[0];
            var txtValue = service.textContent || service.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                services[i].style.display = "";
            } else {
                services[i].style.display = "none";
            }
        }
    }
    function filterServices() {
        var input, filter, serviceRows, serviceNames, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        serviceRows = document.getElementsByClassName("service-row");
        for (i = 0; i < serviceRows.length; i++) {
            serviceNames = serviceRows[i].getElementsByClassName("service-name");
            txtValue = serviceNames[0].textContent || serviceNames[0].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                serviceRows[i].style.display = "";
            } else {
                serviceRows[i].style.display = "none";
            }
        }
    }