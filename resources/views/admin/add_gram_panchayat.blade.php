@extends('layouts.dahboard_layout')

@section('body-page')
    <main id="main" class="main">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ग्राम पंचायत मास्टर प्रविष्टि</h5>
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
                            <form action="{{ route('admin.add_gram_panchayat') }}" method="post" id="gpForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">ब्लॉक कोड <span class="text-danger">*</span></label>
                                        <select class="form-select" id="block_code" name="block_code" required>
                                            <option value="">-- ब्लॉक चुनें --</option>
                                            @foreach ($blocks as $block)
                                                <option value="{{ $block->block_code }}">
                                                    {{ $block->block_code }} - {{ $block->block_name_hin ?: $block->block_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">पंचायत कोड <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="panchayat_code" id="panchayat_code"
                                            placeholder="पंचायत कोड दर्ज करें" required />
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">पंचायत नाम (हिंदी) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="panchayat_name_hin" id="panchayat_name_hin"
                                            placeholder="हिंदी नाम दर्ज करें" required />
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">पंचायत नाम (English) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="panchayat_name_en" id="panchayat_name_en"
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
                        <h5>AWC में उपलब्ध पर मास्टर में Missing ग्राम पंचायत (इन्हें add करना है)</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>GP/NNN Code (नाम)</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                    <th>AWC में ब्लॉक नाम</th>
                                    <th>Master में GP का ब्लॉक</th>
                                    <th>सही GP (Master)</th>
                                    <th>कार्रवाई</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($missingPanchayats as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $item->gp_nnn_code }}
                                            @if (!empty($item->master_gp_name))
                                                - {{ $item->master_gp_name }}
                                            @endif
                                        </td>
                                        <td>{{ $item->awc_count }}</td>
                                        <td>{{ $item->awc_block_name ?: '-' }}</td>
                                        <td>{{ $item->master_block_by_gp ?: '-' }}</td>
                                        <td>
                                            @php
                                                $gpOptions = $item->gp_options ?? [];
                                            @endphp
                                            @if (!empty($gpOptions))
                                                <select class="form-select form-select-sm gp-select"
                                                    data-old="{{ $item->gp_nnn_code }}"
                                                    data-block="{{ $item->awc_block_name }}">
                                                    <option value="">-- GP चुनें --</option>
                                                    @foreach ($gpOptions as $opt)
                                                        <option value="{{ $opt['code'] }}">
                                                            {{ $opt['code'] }} - {{ $opt['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <span class="text-muted">No GP options</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($gpOptions) && !empty($item->gp_nnn_code))
                                                <button type="button" class="btn btn-sm btn-outline-danger update-gp-by-block"
                                                    data-old="{{ $item->gp_nnn_code }}"
                                                    data-block="{{ $item->awc_block_name }}">
                                                    Update करें
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-primary fill-gp-code"
                                                    data-code="{{ $item->gp_nnn_code }}">
                                                    Add करें
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">कोई missing पंचायत नहीं मिली।</td>
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
                        <h5>Project से Match हो चुकी ग्राम पंचायत सूची</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>GP/NNN Code (नाम)</th>
                                    <th>पंचायत नाम (हिंदी)</th>
                                    <th>पंचायत नाम (English)</th>
                                    <th>ब्लॉक (AWC)</th>
                                    <th>ब्लॉक (Master)</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mappedPanchayats as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->gp_nnn_code }} - {{ $item->panchayat_name_hin }}</td>
                                        <td>{{ $item->panchayat_name_hin }}</td>
                                        <td>{{ $item->panchayat_name }}</td>
                                        <td>{{ $item->awc_block_name ?: '-' }}</td>
                                        <td>{{ $item->mapped_block_name ?: $item->block_code }}</td>
                                        <td>{{ $item->awc_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">कोई mapped पंचायत उपलब्ध नहीं है।</td>
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
        $(document).on('click', '.fill-gp-code', function() {
            const gpCode = $(this).data('code');
            $('#panchayat_code').val(gpCode).focus();
            $('html, body').animate({
                scrollTop: $('#gpForm').offset().top - 120
            }, 200);
        });

        $('#gpForm').on('submit', function(e) {
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

                    if (resp.status === 'confirm') {
                        Swal.fire({allowOutsideClick:false, 
                            icon: 'info',
                            title: 'सूचना',
                            text: resp.message || 'क्या आप जारी रखना चाहते हैं?',
                            showCancelButton: true,
                            confirmButtonText: 'हाँ',
                            cancelButtonText: 'नहीं'
                        }).then((result) => {
                            if (!result.isConfirmed) return;
                            const confirmForm = new FormData($('#gpForm')[0]);
                            confirmForm.append('confirm_duplicate', '1');
                            $.ajax({
                                url: $('#gpForm').attr('action'),
                                method: 'POST',
                                data: confirmForm,
                                contentType: false,
                                processData: false,
                                dataType: 'json',
                                success: function(confirmResp) {
                                    if (confirmResp.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: confirmResp.message,
                                            allowOutsideClick: false
                                        }).then(() => window.location.reload());
                                        return;
                                    }
                                    Swal.fire({allowOutsideClick:false, 
                                        icon: 'info',
                                        title: 'सूचना',
                                        text: confirmResp.message || 'डेटा सेव नहीं हो सका।',
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
                        return;
                    }

                    Swal.fire({allowOutsideClick:false, 
                        icon: 'info',
                        title: 'सूचना',
                        text: resp.message || 'डेटा सेव नहीं हो सका।',
                    });
                },
                error: function(xhr) {
                    let message = 'डेटा सेव नहीं हो सका।';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({allowOutsideClick:false, 
                        icon: 'info',
                        title: 'सूचना',
                        text: message
                    });
                }
            });
        });

        $(document).on('click', '.update-gp-by-block', function() {
            const gpCodeOld = $(this).data('old');
            const awcBlock = $(this).data('block');
            const select = $(this).closest('tr').find('.gp-select');
            const gpCodeNew = select.val();

            if (!gpCodeNew) {
                Swal.fire({allowOutsideClick:false, 
                    icon: 'info',
                    title: 'GP चुनें',
                    text: 'कृपया सही GP code चुनें।'
                });
                return;
            }

            Swal.fire({allowOutsideClick:false, 
                icon: 'info',
                title: 'क्या आप sure हैं?',
                text: `GP ${gpCodeOld} को ${gpCodeNew} में update करना है (block: "${awcBlock}")?`,
                showCancelButton: true,
                confirmButtonText: 'हाँ, update करें',
                cancelButtonText: 'रद्द करें'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ route('admin.update_awc_gp_by_block') }}",
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        old_gp_nnn_code: gpCodeOld,
                        new_gp_nnn_code: gpCodeNew,
                        awc_block_name: awcBlock
                    },
                    success: function(resp) {
                        Swal.fire({
                            icon: resp.status === 'success' ? 'success' : 'warning',
                            title: resp.message || 'Completed',
                            allowOutsideClick: false
                        }).then(() => window.location.reload());
                    },
                    error: function(xhr) {
                        let msg = 'Auto-fix नहीं हो सका।';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({allowOutsideClick:false, 
                            icon: 'info',
                            title: 'सूचना',
                            text: msg
                        });
                    }
                });
            });
        });
    </script>
@endsection
