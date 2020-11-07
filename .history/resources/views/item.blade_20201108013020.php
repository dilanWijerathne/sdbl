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



        <script>

            function acc(){
                var nic = $('#nic').val();
                alert(nic);
            }
        </script>

    </head>


    <body>


        <div class="container">
            <div class="row">
              <div class="col-sm">




                <h2>Applicant Basic Details.</h2>

                @foreach ($ar['Applicant'] as $a)
                <input type="hidden"  id="nic" value="{{ $a->nic }}">
                <p>Title :{{ $a->title }}</p>
                <p>Display name :  {{ $a->display_name }}</p>
                <p>Display name :  {{ $a->display_name }}</p>
                <p>Full name :  {{ $a->full_name }}</p>
                <p>NIC :  : {{ $a->nic }} </p>
                <p>Display name :  {{ $a->display_name }}</p>
                <p>Date of Birth :   {{ $a->birth_year }}::  {{ $a->birth_month }}:: {{ $a->birth_day }} </p>
                <p>Gender :  {{ $a->sex }}</p>
                <p>Applicant status :  {{ $a->applicant_status }}</p>
                <p>Account going to open  :{{ $a->applicant_going_to_open }}</p>
                <p>Individual account_type :  {{ $a->applicant_individual_account_type }}</p>
                <p>Mobile :  {{ $a->primary_mobile_number }}</p>
                <p>Seconday Mobile  :{{ $a->secondary_mobile_number }}</p>
                <p>Email  :{{ $a->email }}</p>
                <p>Address  :{{ $a->address }}</p>
                <p>Address same as NIC :  :{{ $a->same_nic_address }}</p>
                <p>Security Question  :{{ $a->security_question }}</p>
                <p>Created at   :{{ $a->created_at }}</p>


                <br>
                <br>
                @endforeach


                <h2>**************************************************************</h2>
                <h2>Work Place info   : </h2>


                @foreach ($ar['WorkPlace'] as $a)

                <p>Employer name  : {{ $a->name }}</p>
                <p>Position  : {{ $a->position }}</p>
                <p>Employer address  : {{ $a->address }}</p>

                <p>Work plz Telephone : {{ $a->telephone }}</p>
                <p>Monly income/ Salary  : {{ $a->income_monthly }}</p>
                <p>Other incomes : {{ $a->other_income }}</p>
                <p>Source other income : {{ $a->source_other_income }}</p>
                <p>Created at   : {{ $a->created_at }}</p>


                <br>
                <br>
                @endforeach


                <h2>**************************************************************</h2>
                <h2>KYC : </h2>


                @foreach ($ar['KYC'] as $a)
                <p>PEP   :{{ $a->pep }}</p>
                <p>PEP relationship   :{{ $a->pep_relationship }}</p>
                <p>{{ $a->json }}</p>
                <p>Created at   :{{ $a->created_at }}</p>
                <br>
                <br>


                @endforeach




                <h2>**************************************************************</h2>
                <h2>Nominees  : </h2>


                @foreach ($ar['Nominee'] as $a)
                <p>{{ $a->json }}</p>
                <p>Created at   :{{ $a->created_at }}</p>
                <br>
                <br>
                @endforeach



              </div>
              <div class="col-sm">

               <button onclick="acc()"> Approved Create account ......</button>
              </div>
              <div class="col-sm">
                pending ....
              </div>
            </div>
          </div>


    </body>
</html>
