@extends('layouts.dahboard_layout')

@section('styles')
@endsection

@section('body-page')
    <main id="main" class="main">
        <div class="pagetitle">
            <h5 class="fw-bold">विज्ञापन सूची</h5>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/candidate/candidate-dashboard') }}">होम</a></li>
                    <li class="breadcrumb-item active">विज्ञापन सूची</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-card-list"> </i> विज्ञापन सूची</h5>
                        <p class="mt-2 text-secondary">जिस प्रकार की भर्ती के लिए आप आगे बढ़ना चाहते हैं, उस पर क्लिक करें।
                        </p>
                    </div>
                    <div class="table-responsive container" style="margin-top: 20px; margin-bottom: 20px;">
                        <table id="datatable" class="table table-striped table-bordered cell-border">
                            <thead>
                                <tr>
                                    <th>क्रमांक</th>
                                    <th>विज्ञापन शीर्षक</th>
                                    <th>पद शीर्षक</th>
                                    <th>पात्रता (आयु के अनुसार)</th>
                                    <th>अंतिम तिथि</th>
                                    <th>दस्तावेज</th>
                                    <th>कार्रवाई</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schemes as $index => $scheme)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $scheme->Advertisement_Title }}</td>
                                        <td>
                                            @php
                                                $posts = explode(', ', $scheme->Post_Titles);
                                                $eligibilities = explode(', ', $scheme->Eligibility_Status);
                                            @endphp
                                            @foreach ($posts as $key => $post)
                                                <div>
                                                    {{ $key + 1 }}. {{ $post }}
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($eligibilities as $key => $eligibility_check)
                                                <div>
                                                    {{ $key + 1 }}.
                                                    @if ($eligibility_check == 'Eligible')
                                                        <span class="text-success">{{ $eligibility_check }}</span>
                                                    @elseif($eligibility_check == 'Not Eligible')
                                                        <span class="text-danger">{{ $eligibility_check }}</span>
                                                    @else
                                                        <span class="text-warning">{{ $eligibility_check }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>{{ date('d-m-Y', strtotime($scheme->End_date)) }}</td>
                                        
                                        <td>
                                            @if ($scheme->Advertisement_Document)
                                                <button type="button" class="btn btn-sm text-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#documentModal{{ $index }}"
                                                    style="text-decoration: none">
                                                    फाइल देखें
                                                </button>
                                        @php
                                            $file_path = '';
                                            if (isset($scheme->Advertisement_Document) && $scheme->Advertisement_Document) {
                                                $file_path = asset('uploads/' . $scheme->Advertisement_Document);
                                                if (config('app.env') === 'production') {
                                                    $file_path = config('custom.file_point') . $scheme->Advertisement_Document;
                                                }
                                            }
                                        @endphp

                                                <!-- Modal -->
                                                <div class="modal fade" id="documentModal{{ $index }}"
                                                    tabindex="-1" aria-labelledby="documentModalLabel{{ $index }}"
                                                    aria-hidden="true" data-bs-backdrop="static">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="documentModalLabel{{ $index }}">
                                                                    {{ $scheme->Advertisement_Title }}</h5>
                                                                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                                                            </div>
                                                            <div class="modal-body">
                                                                <iframe
                                                                    src="{{ $file_path }}"
                                                                    width="100%" height="500px" style="border: none;">
                                                                </iframe>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">बंद करें</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">कोई दस्तावेज नहीं</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $hasEligible = in_array('Eligible', $eligibilities);
                                            @endphp

                                            @if ($hasEligible)
                                                <a
                                                    href="{{ url('/candidate/user-apply-post/' . md5($scheme->Advertisement_ID)) }}">
                                                    <i class="bi bi-arrow-right"></i> आगे बढ़ें
                                                </a>
                                            @else
                                                <span class="text-danger">Not Eligible</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- End #main -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $('#datatable').DataTable({
                "autoWidth": false,
                "paging": true,
                "lengthMenu": [10, 25, 50, 100],
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'fB>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>", // Customize the layout
                buttons: [
                    'excel'
                ]
            });
        });
    </script>
@endsection
