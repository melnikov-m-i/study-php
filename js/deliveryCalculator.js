window.onload = function () {

    var formDeliveryCalculator = document.getElementsByName("formDeliveryCalculator")[0];

    formDeliveryCalculator.onsubmit = function(event) {
        event.preventDefault();

        let data = new FormData(formDeliveryCalculator);

        fetch('./calculateDelivery.php', {
            body: data,
            method: "POST"
        }).then(function(response) {
                var contentType = response.headers.get("content-type");
                if(contentType && contentType.includes("text/html")) {
                    return response.text();
                }
                throw new TypeError("Данные получены не формате HTML");
            })
            .then(function(html) {
                let infoCalculate = document.querySelector('.info-calculate');
                infoCalculate.innerHTML = html;
            })
            .catch(function(error) {
                let infoCalculate = document.querySelector('.info-calculate');
                infoCalculate.innerHTML = '<div class="error-calculate"><p>'+error.message+'</p></div>';
            });

    }

};
