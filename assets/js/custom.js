$(document)
    .ready(function () {

        $('.event-gallery').bxSlider({auto: true, mode: 'fade', pager: false});

        $('.no-enter input, .no-enter textarea').keydown(function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });

        /* Password toggle */
        $('#password-toggle').change(function (e) {
            var target = document.getElementById('signup-pwd');
            target.type = target.type === "password"
                ? "text"
                : "password"
        });

        function calculate(element, element_totalbox) {
            var val = element.val(),
                prc = element.data('price'),
                subtotal = val * prc;
            element_totalbox.text(subtotal);

            setTimeout(function () {
                var total_fields = $('.ticket-totals'),
                    grand = 0;

                total_fields.each(function (index, value) {
                    var subtotal = $(this).text();
                    grand += Number(subtotal);
                });
                $('.grand-totals').text(grand);
            }, 100)
        }

        function totals(selectbox, totalbox) {
            calculate($(selectbox), $(totalbox));

            $(selectbox).change(function () {
                calculate($(selectbox), $(totalbox));
            });
        }

        totals('.ticket-qty-1', '.ticket-totals-1');
        totals('.ticket-qty-2', '.ticket-totals-2');
        totals('.ticket-qty-3', '.ticket-totals-3');
        totals('.ticket-qty-4', '.ticket-totals-4');

        $('.booking-form').submit(function (e) {
            // e.preventDefault();

            var evt_day = $('input[name=event_day]').val(),
                tkt_qty = $('input[name=tkt_qty]').val(),
                cst_nm = $('input[name=customer_names]').val(),
                cst_email = $('input[name=email]').val(),
                cst_phone = $('input[name=mobile_number]').val(),
                mps_code = $('input[name=mpesa_code]').val();

            console.log(evt_day);
            console.log(tkt_qty);
            console.log(cst_nm);
            console.log(cst_email);
            console.log(cst_phone);
            console.log(mps_code);
        });
    });