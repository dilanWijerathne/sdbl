<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SDB</title>

        <script src="//code.jquery.com/jquery-3.5.1.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet">



    </head>


    <body>


        <div class="container">
            <div class="row">
              <div class="col-sm">



title: "Ms",
surname: "undefined",
initials: "",
display_name: "Hatchet",
full_name: "Hatchet",
f_name: "Hatchet",
nic: "900103775V",
birth_year: 1990,
birth_month: "1",
birth_day: 10,
sex: "Male",
applicant_status: "Retired (Non-Government)",
applicant_going_to_open: "Fixed Deposits",
applicant_individual_account_type: null,
primary_mobile_number: "0772772779",
secondary_mobile_number: "900103775V",
email: "Nvhfgvu@ugu.com",
address: "Gh",
living_place_dif: null,
district: "Colombo",
same_nic_address: "",
security_question: "Fygydygu",
existing_customer: null,
created_at: "2020-11-07T17:28:31.000000Z",
updated_at: "2020-11-07T17:28:31.000000Z"


                @foreach ($ar as $a)
                <p>Title :{{ $a->title }}</p><br>
                <p>Display name :  :{{ $a->display_name }}</p><br>
                @endforeach




              </div>
              <div class="col-sm">
                One of three columns
              </div>
              <div class="col-sm">
                One of three columns
              </div>
            </div>
          </div>


    </body>
</html>
