<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title> {{ env('APP_NAME', 'Kiosque') }} -
        @section('title')
        @show
    </title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fa/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap.min.js"></script>

</head>



<body id="page-top">
    <div id="wrapper">
        @include('dashboard/sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('dashboard/navbar')
                <div class="container-fluid">
                    @section('content')
                    @show
                </div>
            </div>
            @include('dashboard/footer')
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
</body>

<script src="{{ asset('/assets/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/assets/bootstrap/js/theme.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"
    integrity="sha512-LUKzDoJKOLqnxGWWIBM4lzRBlxcva2ZTztO8bTcWPmDSpkErWx0bSP4pdsjNH8kiHAUPaT06UXcb+vOEZH+HpQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $.extend(true, $.fn.dataTable.defaults, {

        "language": {
            "search": "Rechercher:",

            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            },
            "decimal": ".",
            "emptyTable": "Aucun ligne ",
            "info": "",
            "infoFiltered": "",
            "infoEmpty": "",
            "lengthMenu": "",

        },

    });
    $("input").addClass("shadow-none");
</script>

</html>
