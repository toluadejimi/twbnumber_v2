@extends('layout.main')
@section('content')



    <div class="container p-5">



        <div class="row mt-5">


            <div class="text-stone-900 text-center text-xl mb-3 font-bold  whitespace-nowrap mt-10">
                Resolve Deposit
            </div>



        <div class="text-center mt-3">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif
        </div>
        




            <div class="col-lg-5 col-sm-12">
                <div class="card border-0 shadow-lg p-3 mb-5 bg-body rounded-40">
                    <div class="card-body">
                        <div class="">

                            <form action="resolve-now" method="POST">
                                @csrf
                          

                                    <p> Resolve pending transactions by using your bank session ID / Refrence No on your
                                        transaction recepit
                                    <p>

                                    <h6>{{$ref}}</h6>

                                    <label class="my-3">Select Bank</label>
                                    <select class="form-control" required name="bank_type">
                                        <option value="">Select option</option>
                                        <option value="opay">OPAY</option>
                                        <option value="palmpay">PALMPAY</option>
                                        <option value="providus">PROVIDUS</option>
                                    </select>

                                    <label class="my-3">Enter Session ID or Reference</label>
                                    <div>
                                        <input type="text" name="session_id" required
                                               class="form-control p-2 text-dark mb-3" placeholder="Enter session ID or Reference">
                                               
                                               
                                        <small class="text-danger my-2">If transaction is from OPAY OR PALMPAY use the 3 letter generated as reference</small>
                                        <input hidden type="text" name="ref_id"
                                               value="{{ $ref }}" required class="">

                                    </div>

                                <button type="submit" class="text-white btn btn-block btn-dark my-4">
                                    Add Funds
                                </button>        
                            </form>
                        </div>
                        


                        </div>

                    </div>


                </div>
            </div>




           
        </div>




       



        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex flex-wrap justify-content-center px-0">
                <div class="card-body p-5">
                    
        </ul>






    </div>

@endsection
