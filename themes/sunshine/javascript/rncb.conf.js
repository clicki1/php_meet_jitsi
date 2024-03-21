jQuery(document).ready(function() {
    const observer = new MutationObserver(function(mutations) {
        const iframe = document.querySelector("iframe[name='hppIFrame']");
        if (iframe) {
            iframe.style.removeProperty('display');
            observer.disconnect();
        }
    });
    observer.observe(document, {
        attributes: false,
        childList: true,
        characterData: false,
        subtree: true
    });

    console.log('initWidget', $( "#initWidget" ))
    $( "#initWidget" ).on( "click", function() {
        
        const orderData = {
            'order': {
                "typeRid": "Sale Order Type_Single_CVV",
                "amount": document.querySelector('#amount').value.toString(),
                "currency": "RUB",
                "description": "",
                "language": "ru",
                "srcEmail": document.getElementById('fld_email').value.toString(),

                "hppRedirectUrl": location.href
            },
            'auth': {
                'userName': 'TerminalSys/50000350',
                'password': 'Dec@25122023'
            },
            'terminalId': '1384',
            'merchantId': '1384'
        };
        if (false) {
            orderData.order.description = orderData.order.description.substr(0, 140);
        }
        const listeners = {
            onSuccess(data) {
                console.log('Заказ оплачен', data);
            },
            onFail(data) {
                console.log('Произошла ошибка', data);
            }
        };
        const hppIntegrationApi = new HppIntegrationApi(orderData, listeners);
        const payButton = document.getElementById('continueButton');
        hppIntegrationApi.createWidget(document.getElementById('widgetContainer'), {
            payButton
        });
        payButton.click();
    });
});