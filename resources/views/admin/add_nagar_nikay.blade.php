@extends('layouts.dahboard_layout')

@section('body-page')
    <main id="main" class="main">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>नगर निकाय मास्टर प्रविष्टि</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mt-3" role="alert">
                            वर्तमान प्रोजेक्ट कोड: <strong>{{ $projectCode ?: '-' }}</strong> |
                            जिला LGD कोड: <strong>{{ $districtLgdCode ?: '-' }}</strong>
                        </div>

                        @if (!$projectCode || !$districtLgdCode)
                            <div class="alert alert-warning" role="alert">
                                सेशन में प्रोजेक्ट/जिला जानकारी उपलब्ध नहीं है। कृपया पुनः लॉगिन करें।
                            </div>
                        @else
                            <form action="{{ route('admin.add_nagar_nikay') }}" method="post" id="nagarForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">नगर निकाय कोड <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nagar_code" id="nagar_code"
                                            placeholder="नगर निकाय कोड दर्ज करें" required />
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">नगर निकाय नाम (हिंदी) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nagar_name_hin" id="nagar_name_hin"
                                            placeholder="हिंदी नाम दर्ज करें" required />
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">नगर निकाय नाम (English) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nagar_name_en" id="nagar_name_en"
                                            placeholder="English name enter करें" required />
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">दर्ज करें</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>AWC में उपलब्ध पर मास्टर में Missing नगर निकाय (इन्हें add करना है)</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>नगर निकाय कोड</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                    <th>कार्रवाई</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($missingNagars as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->gp_nnn_code }}</td>
                                        <td>{{ $item->awc_count }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary fill-nagar-code"
                                                data-code="{{ $item->gp_nnn_code }}">
                                                Add करें
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">कोई missing नगर निकाय नहीं मिला।</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Project से Match हो चुके नगर निकाय</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>नगर निकाय कोड (नाम)</th>
                                    <th>नाम (हिंदी)</th>
                                    <th>नाम (English)</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mappedNagars as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->gp_nnn_code }} - {{ $item->nnn_name }}</td>
                                        <td>{{ $item->nnn_name }}</td>
                                        <td>{{ $item->nnn_name_en }}</td>
                                        <td>{{ $item->awc_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">कोई mapped नगर निकाय उपलब्ध नहीं है।</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.fill-nagar-code', function() {
            const nagarCode = $(this).data('code');
            $('#nagar_code').val(nagarCode).focus();
            $('html, body').animate({
                scrollTop: $('#nagarForm').offset().top - 120
            }, 200);
        });

        $('#nagarForm').on('submit', function(e) {
            e.preventDefault();

            const form = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: form,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: resp.message,
                            allowOutsideClick: false
                        }).then(() => window.location.reload());
                        return;
                    }

                    Swal.fire({allowOutsideClick:false, 
                        icon: 'info',
                        title: 'सूचना',
                        text: resp.message || 'डेटा सेव नहीं हो सका।',
                    });
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'डेटा सेव नहीं हो सका।';
                    Swal.fire({allowOutsideClick:false, 
                        icon: 'info',
                        title: 'सूचना',
                        text: msg,
                    });
                }
            });
        });
    </script>
@endsection
