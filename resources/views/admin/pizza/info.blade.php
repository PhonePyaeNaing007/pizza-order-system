@extends('admin.layout.app')

@section('content')
<div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <div class="row mt-4">
          <div class="col-9 offset-2 mt-2">
            <div class="col-md-11">
                <a href="{{ route('admin#pizza') }}" class="text-dark text-decoration-none"><div class="mb-3 "><i class="fas fa-arrow-left"></i>back</div></a>
              <div class="card">
                <div class="card-header p-2">
                  <legend class="text-center">Pizza Information</legend>
                </div>
                <div class="card-body">
                  <div class="tab-content">
                    <div class="active tab-pane d-flex justify-content-center" id="activity">
                        <div class="mt-2 text-center pr-4 pt-4">
                            <img class="img-thumbnail" src="{{ asset('uploads/'.$pizza->image) }}" style="width: 200px;height:200px">
                        </div>
                        <div class="">
                            <div class="mt-3">
                                <b>Name</b>: <span>{{ $pizza->pizza_name }}</span>
                            </div>
                            <div class="mt-3">
                                <b>Price</b>: <span>{{ $pizza->price }} MMK</span>
                            </div>
                            <div class="mt-3">
                                <b>Publish Status</b>:
                                <span>
                                    @if ($pizza->publish_status==1)
                                        Yes
                                    @else
                                        No
                                    @endif
                                </span>
                            </div>
                            <div class="mt-3">
                                <b>Category</b>: <span>{{ $pizza->category_id }}</span>
                            </div>
                            <div class="mt-3">
                                <b>Discount Price</b>: <span>{{ $pizza->discount_price }} MMK</span>
                            </div>
                            <div class="mt-3">
                                <b>Buy One Get One</b>:
                                <span>
                                    @if ($pizza->buy_one_get_one_status==1)
                                        Yes
                                    @else
                                        No
                                    @endif
                                </span>
                            </div>
                            <div class="mt-3">
                                <b>Waiting Time</b>: <span>{{ $pizza->waiting_time }} Minutes</span>
                            </div>
                            <div class="mt-3">
                                <b>Description</b>: <span>{{ $pizza->description }}</span>
                            </div>
                        </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection
