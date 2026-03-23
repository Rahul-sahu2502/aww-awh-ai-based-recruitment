@extends('layouts.dahboard_layout')

@section('body-page')
    <main id="main" class="main">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ग्राम (Village) मास्टर प्रविष्टि</h5>
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
                            <form action="{{ route('admin.add_village') }}" method="post" id="villageForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">ब्लॉक (कोड - नाम) <span class="text-danger">*</span></label>
                                        <select class="form-select" id="block_code" name="block_code" required>
                                            <option value="">-- ब्लॉक चुनें --</option>
                                            @foreach ($blocks as $block)
                                                <option value="{{ $block->block_code }}">
                                                    {{ $block->block_code }} - {{ $block->block_name_hin ?: $block->block_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">पंचायत (कोड - नाम) <span class="text-danger">*</span></label>
                                        <select class="form-select" id="panchayat_code" name="panchayat_code" required>
                                            <option value="">-- पंचायत चुनें --</option>
                                            @foreach ($panchayats as $gp)
                                                <option value="{{ $gp->panchayat_lgd_code }}">
                                                    {{ $gp->panchayat_lgd_code }} - {{ $gp->panchayat_name_hin }} ({{ $gp->block_name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">गाँव कोड <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="village_code" id="village_code"
                                            placeholder="गाँव कोड दर्ज करें" required />
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">गाँव नाम (हिंदी) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="village_name_hin" id="village_name_hin"
                                            placeholder="हिंदी नाम दर्ज करें" required />
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">गाँव नाम (English) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="village_name_en" id="village_name_en"
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
                        <h5>AWC में उपलब्ध पर मास्टर में Missing ग्राम (इन्हें add करना है)</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>GP/NNN Code (नाम)</th>
                                    <th>गाँव कोड</th>
                                    <th>AWC में ब्लॉक नाम</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                    <th>सही GP (Master)</th>
                                    <th>स्थिति</th>
                                    <th>कार्रवाई</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($missingVillages as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->gp_nnn_code }} - {{ $item->panchayat_name_hin ?? '-' }}</td>
                                        <td>{{ $item->gram_ward_code }}</td>
                                        <td>{{ $item->awc_block_name ?: '-' }}</td>
                                        <td>{{ $item->awc_count }}</td>
                                        <td>
                                            @if (($item->row_type ?? '') === 'gp_mismatch' && !empty($item->master_panchayat_lgd_code))
                                                <select class="form-select form-select-sm" disabled>
                                                    <option>
                                                        {{ $item->master_panchayat_lgd_code }} - {{ $item->panchayat_name_hin ?? '-' }} ({{ $item->awc_block_name ?? '-' }})
                                                    </option>
                                                </select>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (($item->row_type ?? '') === 'block_mismatch')
                                                Block Mismatch
                                            @elseif (($item->row_type ?? '') === 'gp_mismatch')
                                                GP Mismatch
                                            @else
                                                Missing
                                            @endif
                                        </td>
                                        <td>
                                            @if (in_array(($item->row_type ?? ''), ['block_mismatch', 'gp_mismatch'], true))
                                                <button type="button" class="btn btn-sm btn-outline-danger fix-village-block"
                                                    data-old="{{ $item->gp_nnn_code }}"
                                                    data-village="{{ $item->gram_ward_code }}"
                                                    data-block="{{ $item->awc_block_name }}">
                                                    Update GP
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-primary fill-village-code"
                                                    data-gp="{{ $item->gp_nnn_code }}"
                                                    data-code="{{ $item->gram_ward_code }}">
                                                    Add करें
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">कोई missing गाँव नहीं मिले।</td>
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
                        <h5>GP + Block + Village Code Match (Project)</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>क्र.</th>
                                    <th>GP/NNN Code</th>
                                    <th>गाँव कोड</th>
                                    <th>गाँव नाम (हिंदी)</th>
                                    <th>गाँव नाम (English)</th>
                                    <th>ब्लॉक (AWC)</th>
                                    <th>ब्लॉक (Master)</th>
                                    <th>AWC रिकॉर्ड्स</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($matchedVillages as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->gp_nnn_code }} - {{ $item->panchayat_name }}</td>
                                        <td>{{ $item->gram_ward_code }}</td>
                                        <td>{{ $item->village_name_hin }}</td>
                                        <td>{{ $item->village_name }}</td>
                                        <td>{{ $item->awc_block_name ?: '-' }}</td>
                                        <td>{{ $item->mapped_block_name ?: '-' }}</td>
                                        <td>{{ $item->awc_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">कोई matched गाँव उपलब्ध नहीं है।</td>
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
        $(document).on('click', '.fill-village-code', function() {
            const gpCode = $(this).data('gp');
            const villageCode = $(this).data('code');

            $('#panchayat_code').val(gpCode);
            $('#village_code').val(villageCode).focus();
            $('html, body').animate({
                scrollTop: $('#villageForm').offset().top - 120
            }, 200);
        });

        $('#villageForm').on('submit', function(e) {
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

        $(document).on('click', '.fix-village-block', function() {
            const oldGp = $(this).data('old');
            const villageCode = $(this).data('village');
            const awcBlock = $(this).data('block');

            Swal.fire({allowOutsideClick:false, 
                icon: 'info',
                title: 'क्या आप sure हैं?',
                text: `GP ${oldGp} को village ${villageCode} के लिए update करना है?`,
                showCancelButton: true,
                confirmButtonText: 'हाँ, update करें',
                cancelButtonText: 'रद्द करें'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ route('admin.update_awc_village_gp_by_block') }}",
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        old_gp_nnn_code: oldGp,
                        village_code: villageCode,
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
                        let msg = 'Update नहीं हो सका।';
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
