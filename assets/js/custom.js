$(document).ready(function () {
    var dt = $('.dt'),
        datePicker = $('.datepicker'),
        homeSlider = $('.home-slider');

    if (dt.length > 0) {
        $('.dt').dataTable({
            sort: [], // Prevent autosort
            dom: 'Bfrtip',
            //lengthChange: false,
            lengthMenu: [
                [10, 25, 50, 75, 100, -1],
                ['10 rows', '25 rows', '50 rows', '75 rows', '100 rows', 'Show all']
            ],
            buttons: [
                'pageLength',
                {
                    extend: 'copyHtml5',
                    text: 'Copy',
                    titleAttr: 'Copy',
                    exportOptions: {
                        columns: [0, ':visible']
                    }
                }, {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                }, {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    titleAttr: 'PDF',
                    exportOptions: {
                        columns: ':visible'
                    }
                }/*,{
                    extend: 'colvis',
                    className: 'hidden-print',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Hide Columns'
                }*/
            ]
        })
    }

    $('.no-enter input, .no-enter textarea').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault()
            return false
        }
    })

    /* Password toggle */
    $('#password-toggle').change(function (e) {
        var target = document.getElementById('signup-pwd')
        target.type = target.type === "password"
            ? "text"
            : "password"
    })

    /*  Datepicker  */
    if (datePicker.length > 0) {
        datePicker.datepicker({
            format: 'yyyy-mm-dd',
            // startDate: '-0d',
            setDate: new Date()
        }).on('changeDate', function (e) {
            $('.datepicker.dropdown-menu').hide()
        })
    }

    if (homeSlider.length > 0) {
        homeSlider.slick({
            dots: false,
            arrows: false,
            infinite: true,
            speed: 1000,
            fade: true,
            autoplay: true,
            autoplaySpeed: 2000
        })
    }
})