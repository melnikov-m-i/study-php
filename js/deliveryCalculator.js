document.addEventListener("DOMContentLoaded", ready);

function ready() {
    var formDeliveryCalculator = document.querySelector('form[name="formDeliveryCalculator"]');

    if(formDeliveryCalculator) {
        formDeliveryCalculator.addEventListener('submit', function(event) {
            event.preventDefault();

            fetch('./calculateDelivery.php', {
                body: new FormData(this),
                method: "POST"
            }).then(function(response) {
                    var contentType = response.headers.get("content-type");

                    if(contentType && contentType.includes("application/json")) {
                        return response.json();
                    }

                    throw new TypeError("Данные получены не формате HTML");
                })
                .then(function(data) {
                    let divMessage = document.createElement('div');
                    divMessage.className = data['status'] == "OK" ? "success-calculate" : "";
                    divMessage.insertAdjacentHTML('afterBegin', '<p>' + data['message'] + '</p>');
                    document.querySelector('.info-calculate').innerHTML = divMessage.outerHTML;
                })
                .catch(function(error) {
                    document.querySelector('.info-calculate').innerHTML = '<div class="error-calculate">' +
                        '<p>' + error + '</p></div>';
                });
        });
    }
}
