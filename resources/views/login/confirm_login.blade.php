@extends('layouts.dahboard_layout')

@section('body-page')
    <main class="main">
        <div class="pagetitle">
            <h1>Confirm Login</h1>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">पुष्टि करें</h5>
                    <p>ऐसा प्रतीत होता है कि आपने पहले किसी अन्य डिवाइस/ब्राउज़र पर लॉगिन किया हुआ है। क्या आप पुराने सत्र
                        को समाप्त करके नया सत्र बनाना चाहते हैं?</p>

                    <form method="post" action="{{ route('login.confirm.post') }}">
                        @csrf
                        <button type="submit" name="action" value="yes" class="btn btn-danger">Yes - End old session and
                            login</button>
                        <button type="submit" name="action" value="no" class="btn btn-secondary">No - Cancel login</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection