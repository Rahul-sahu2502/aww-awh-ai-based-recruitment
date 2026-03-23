@extends('layouts.dahboard_layout')

@section('body-page')
    <main id="main" class="main">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>वार्ड मास्टर प्रविष्टि</h5>
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
                            <form action="{{ route('admin.add_ward') }}" method="post" id="wardForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">नगर निकाय <span class="text-danger">*</span></label>
                                        <select class="form-select" id="nagar_code" name="nagar_code" required>
                                            <option value="">-- नगर निकाय चुनें --</option>
                                            @foreach ($nagars as $nagar)
                                                <option value="{{ $nagar->std_nnn_code }}">
                                                    {{ $nagar->std_nnn_code }} - {{ $nagar->nnn_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">वार्ड नंबर <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="ward_no" id="ward_no"
                                            placeholder="वार्ड नंबर दर्ज करें" required />
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">वार्ड नाम <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ward_name" id="ward_name"
                                            placeholder="वार्ड नाम दर्ज करें" required />
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
                        <h5>AWC में उपलब्ध पर मास्टर में Missing वार्ड (इन्हें add करना है)</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>नगर निकाय कोड</th>
                                    <th>वार्ड नंबर</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                    <th>कार्रवाई</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($missingWards as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->gp_nnn_code }}</td>
                                        <td>{{ $item->gram_ward_code }}</td>
                                        <td>{{ $item->awc_count }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary fill-ward"
                                                data-nagar="{{ $item->gp_nnn_code }}"
                                                data-ward="{{ $item->gram_ward_code }}">
                                                Add करें
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">कोई missing वार्ड नहीं मिला।</td>
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
                        <h5>Project से Match हो चुके वार्ड</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>नगर निकाय कोड</th>
                                    <th>नगर निकाय नाम</th>
                                    <th>वार्ड नंबर</th>
                                    <th>वार्ड नाम</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($matchedWards as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->gp_nnn_code }}</td>
                                        <td>{{ $item->nnn_name }}</td>
                                        <td>{{ $item->ward_no }}</td>
                                        <td>{{ $item->ward_name }}</td>
                                        <td>{{ $item->awc_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">कोई mapped वार्ड उपलब्ध नहीं है।</td>
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
        $(document).on('click', '.fill-ward', function() {
            const nagarCode = $(this).data('nagar');
            const wardNo = $(this).data('ward');
            $('#nagar_code').val(nagarCode);
            $('#ward_no').val(wardNo).focus();
            $('html, body').animate({
                scrollTop: $('#wardForm').offset().top - 120
            }, 200);
        });

        $('#wardForm').on('submit', function(e) {
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
