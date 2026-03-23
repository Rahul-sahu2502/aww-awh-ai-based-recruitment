@extends('layouts.dahboard_layout')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('body-page')

    <main id="main" class="main">


        <div class="row printable-div">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card container" id="printable-div">
                    <div class="wrapper wrapper-content animated fadeInRight">
                        <div class="ibox-content p-xl">
                            <div class="row">
                                <a href="{{ url('/candidate/view-documents/' . md5($applicant_details->RowID)) }}">दस्तावेज
                                    देखें</a>
                            </div>
                            <hr>
                            @php
                                $file_path = asset('uploads') . '/';
                                if (config('app.env') == 'production') {
                                    $file_path = config('custom.file_point');
                                }
                            @endphp
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-responsive">
                                        <tr>
                                            <td>
                                                प्रति,<br>
                                                संचालक,<br>
                                                संचालनालय महिला एवं बाल विकास विभाग,
                                                <br>अटल नगर, नया रायपुर, (छ.ग.)
                                            </td>
                                            <td align="right">
                                                <img src="{{ $file_path . $applicant_details->Document_Photo }}"
                                                    width="160" height="130"><br>
                                                <img src="{{ $file_path . $applicant_details->Document_Sign }}"
                                                    width="160" height="80">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <h5> व्यक्तिगत जानकारी</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-responsive">
                                        <tr>
                                            <td>1. आवेदनकर्ता आईडी नंबर</td>
                                            <td>{{ $applicant_details->Genearted_AppId }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2. आवेदनकर्ता का पूरा नाम</td>
                                            <td>{{ $applicant_details->First_Name }}&nbsp;{{ $applicant_details->Middle_Name }}&nbsp;{{ $applicant_details->Last_Name }}
                                            </td>
                                        </tr>
                                         <tr>
                                            <td>3. पिता का नाम</td>
                                            <td>{{ $applicant_details->FatherName }}</td>
                                        </tr>
                                        <tr>
                                            <td>4. वर्तमान पता</td>
                                            <td>{{ $applicant_details->Corr_Address }}</td>
                                        </tr>
                                        <tr>
                                            <td>5. स्थायी पता</td>
                                            <td>{{ $applicant_details->Perm_Address }}</td>
                                        </tr>
                                        <tr>
                                            <td>6. जन्मतिथि</td>
                                            <td>{{ date('d-m-Y', strtotime($applicant_details->DOB)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>7. मोबाइल नंबर</td>
                                            <td>{{ $applicant_details->Contact_Number }}</td>
                                        </tr>
                                        <tr>
                                            <td>8.आधार नंबर</td>
                                            <td>{{ $applicant_details->aadharno }}</td>
                                        </tr>
                                        <tr>
                                            <td>9. वर्ग / जाति</td>
                                            <td>{{ $applicant_details->Caste }}</td>
                                        </tr>

                                    </table>
                                </div>
                            </div>

                            <h5> शैक्षणिक योग्यता </h5>
                            <div class="row">
                                <div class="col-md-12">

                                    <table class="table table-responsive table-bordered">
                                        <tr>
                                            <th>क्र. सं.</th>
                                            <th>योग्यता</th>
                                            <th>बोर्ड/विश्वविद्यालय का नाम</th>
                                            <th>उत्तीर्ण वर्ष</th>
                                            <th>प्राप्त अंक</th>
                                            <th>कुल अंक</th>
                                            <th>प्रतिशत</th>
                                        </tr>
                                        @if (@$education_details)
                                            @foreach ($education_details as $education)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $education->Quali_Name }}</td>
                                                    <td>{{ $education->qualification_board }}</td>
                                                    <td>{{ $education->year_passing }}</td>
                                                    <td>{{ $education->obtained_marks }}</td>
                                                    <td>{{ $education->total_marks }}</td>
                                                    <td>{{ $education->percentage }} %</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <hr>

                            <h5> अनुभव एवं अन्य जानकारी</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-responsive table-bordered">
                                        <tr>
                                            <th>संस्था का नाम</th>
                                            <th>संस्था का प्रकार</th>
                                            <th>पदनाम</th>
                                            <th>अनुभव का कार्यक्षेत्र</th>
                                            <th>कुल अनुभव</th>
                                        </tr>
                                        @foreach ($experience_details as $experience)
                                            <tr>
                                                <td>{{ $experience->Organization_Name }}</td>
                                                {{-- <td>{{ $experience->Organization_Type }}</td> --}}
                                                <td>
                                                    {{ $experience->Organization_Type == 1
                                                        ? 'शासकीय'
                                                        : ($experience->Organization_Type == 2
                                                            ? 'अशासकीय'
                                                            : ($experience->Organization_Type == 3
                                                                ? 'अर्ध-शासकीय'
                                                                : 'N/A')) }}
                                                </td>
                                                <td>{{ $experience->Designation }}</td>
                                                <td>{{ $experience->Nature_Of_Work }}</td>
                                                <td>{{ $experience->Total_Experience }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>



                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <form id="myForm">
                                    @csrf
                                    <div class="form-group formField">
                                        <input type="hidden" id="RowID" value="{{ md5($applicant_details->ID) }}">
                                        <button type="button" id="editButton" class="btn btn-success btn-sm">विवरण संपादित
                                            करें</button><br><br>
                                        <br><br>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script>
        document.getElementById('editButton').addEventListener('click', function() {
            var rowid = document.getElementById('RowID').value;


            $.ajax({
                url: '/candidate/user-details-form/' + rowid,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#submit").attr("disabled", true);
                    // Show a loading spinner
                    Swal.fire({
                        title: 'Loading...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function(response) {
                    window.location.href = '/candidate/user-details-form/' + rowid + '/update';
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                },
                complete: function() {
                    $("#submit").attr("disabled", false);
                    // Hide the loading spinner
                    Swal.close();
                }
            });

        });

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("myForm").addEventListener("submit", function(event) {
                event.preventDefault();
                console.log("Form submitted!");
                // Add your form submission logic here
            });
        });
    </script>
@endsection
