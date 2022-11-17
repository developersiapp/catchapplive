<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 12:31 PM
 */ ?>
@extends('backend.layouts.default')
@section('title','Clubs')
@section('content')
<?php $GOOGLE_MAP_KEY = 'AIzaSyAOosj6Hg1fpweT-KC4FmbzOeZyuhzwdvw'; ?>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{$GOOGLE_MAP_KEY}}"></script>




<div class="row">
    <div class="col-lg-6 col-md-9">
        <!-- general form elements -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{isset($club)?'Edit Club':'Add New Club'}}</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form action="{{url('' . env('APP_URL') . '/dashboard/clubs/save-club')}}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($club)?$club->id:''}}">
                <div class="box-body">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            Profile Image
                        </label>
                        <div>
                            @if (isset($club))
                            @if (!empty($club->profile_image) && $club->profile_image!= null && $club->profile_image != " ")
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/uploads/clubs/'.$club->profile_image) !!}  class="img-circle"
                            alt="Club Image">

                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/club.png') !!}  class="img-circle"
                            alt="Club Image">
                            @endif
                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/club.png') !!}  class="img-circle"
                            alt="Club Image">
                            @endif
                        </div>
                        <div class="browse_image">
                            <input type="file" name="club_image" class="form-control  mr-5px" onchange="readURL(this);">
                            @error('club_image')
                            <span class="error-block">{{ $message }}</span>
                            @enderror

                            @if(isset($club))
                            @if(!empty($club->profile_image) && $club->profile_image!= null && $club->profile_image != " ")
                            <a href="{{url('' . env('APP_URL') . '/dashboard/clubs/remove-profile-image/'. $club->id)}}"
                               onclick="return confirm('Do you really want to delete this profile photo?')"
                               class="btn btn-xs btn-danger">
                                <i class="fa fa-image"></i><span> Remove Profile Photo</span>
                            </a>
                            @endif
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12">
                                <label class="control-label">
                                    Street Address
                                </label>
                                <textarea class="form-control" rows="3" placeholder="Enter Street Address"
                                          name="s_address">{{ isset($club)?$club->street_address:old('s_address') }}</textarea>
                                @error('s_address')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12">


                                <label class="control-label">
                                    Club Name
                                </label>
                                <input type="text" class="form-control" name="name"
                                       value="{{ isset($club)?$club->name:old('name') }}"
                                       placeholder="Enter Club's Name"/>
                                @error('name')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">
                                    Email
                                </label>
                                <input type="email" class="form-control" name="email"
                                       value="{{ isset($club)?$club->email:old('email') }}"
                                       placeholder="ex : example@abc.com"/>
                                @error('email')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">
                                    Password
                                </label>
                                <input value="{{isset($club)?$club->password:old('password') }}" type="password"
                                       class="form-control" name="password" minlength="6"
                                       placeholder="Enter Password ">

                                @error('password')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>




                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12">


                                <label class="control-label">
                                    Address
                                </label>
                                <input type="text" class="form-control" name="address"
                                       id="search_location"
                                       data-parsley-maxlength="250" data-parsley-trigger="keyup"
                                       value="{{Request::old('address') ?: ''}}"
                                       placeholder="Search location..."/>
                                @error('address')
                                <span class="error-block">{{ $message }}</span>
                                @enderror

                                <input type="text" class="search_addr"
                                       value="{{ Request::old('address') ?: '' }}"
                                       size="45" required style="display: none" data-parsley-trigger="keyup"
                                       data-parsley-error-message="Search Valid Address.">
                                <input type="hidden" class="search_latitude" name="search_latitude" value="{{ Request::old('search_latitude') ?: '' }}"  size="30">
                                <input type="hidden" class="search_longitude" name="search_longitude" value="{{ Request::old('search_longitude') ?: '' }}"  size="30">

                            </div>
                        </div>
                    </div>




                    {{--                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">--}}
                        {{--                            --}}
                        {{--                            <div class="col-md-6 col-sm-6 col-xs-12">--}}
                            {{--                                <input type="text" value="{{ Request::old('address') ?: '' }}" id="search_location" name="address" --}}
                                                                       {{--                                       data-parsley-maxlength="250" data-parsley-trigger="keyup"--}}
                                                                       {{--                                       class="form-control col-md-7 col-xs-12" required>--}}
                            {{--                                @error('address')--}}
                            {{--                                    <span class="help-block">{{ $errors->first('address') }}</span>--}}
                            {{--                                @enderror--}}

                            {{--                                <input type="text" class="search_addr" value="{{ Request::old('address') ?: '' }}" size="45" required style="display: none" data-parsley-trigger="keyup"  data-parsley-error-message="Search Valid Address.">--}}
                            {{--                            </div>--}}
                        {{--                        </div>--}}

                    <!-- display selected location information -->


                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6">
                                <label class="control-label">
                                    Country
                                </label>

                                <input type="text" id="country" name="country" class="form-control" readonly
                                       value="{{isset($club)?$locations['country']:old('country') }}">
                                @error('country')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-xs-6">
                                <label class="control-label">
                                    State
                                </label>
                                <input type="text" id="state" name="state" class="form-control" readonly
                                       value="{{isset($club)?$locations['state']:old('state') }}">
                                @error('state')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6">
                                <label class="control-label">
                                    City :
                                </label>
                                <input type="text" id="city" name="city" class="form-control" readonly
                                       value="{{isset($club)?$locations['city']:old('city') }}">
                                @error('city')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-xs-6">
                                <label class="control-label">
                                    ZIP :
                                </label>
                                <input type="text" id="zip" name="zip" class="form-control"
                                       value="{{isset($club)?$club->zip:old('zip') }}">
                                @error('zip')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12">
                                <label class="control-label">
                                    Assign Djs :
                                </label>
                                <select class="form-control sel-status" name="dj[]" multiple>
                                    <option value="">Select Dj</option>
                                    <?php $count = 1;?>
                                    @foreach(\catchapp\Models\DJ::all() as $dj)
                                    @if(isset($club))
                                    <?php $dj_ids = \catchapp\Models\Pivot_Dj_Club::query()->where('club_id','=', $club->id)->pluck('dj_id');
                                    $sel_djs=[];
                                    foreach ($dj_ids as $id){
                                        array_push($sel_djs, $id);
                                    }


                                    ?>
                                    <option value="{{$dj->id}}" {{isset($club)?(in_array($dj->id, $sel_djs)?'selected':''):''}}>{{$count++}}
                                        . {{$dj->name}}</option>
                                    @else
                                    <option value="{{$dj->id}}" {{old('dj')?(in_array($dj->id, old('dj'))?'selected':''):''}}>{{$count++}}
                                    . {{$dj->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('dj')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        var states = $('.states');
        var cities = $('.cities');
        $('.countries').change(function (e) {
            e.preventDefault();
            var country_id = $(this).val();
            if (country_id) {
                cities.empty();
                $('<option>').val('').text('Select State First').appendTo(cities);
                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ \Illuminate\Support\Facades\URL::route('clubs.changeStates') }}',
                    data: {
                        country_id: country_id
                    },
                    dataType: 'json',
                    success: function (data) {
                        states.empty();
                        $.each(data.states, function (index, state) {
                            console.log(state);
                            $('<option>').val(state.id).text(state['Title']).appendTo(states);
                        })
                    },
                    error: function (data) {
                        console.log('Error:', data.responseText);
                    }
                });
            }else
            {
//                    states.empty();
//                    cities.empty();
//                    $('<option>').val('').text('Select Country First').appendTo(states);
//                    $('<option>').val('').text('Select State First').appendTo(cities);

            }
        });

        states.change(function (e) {
            e.preventDefault();
            var state_id = $(this).val();
            if(state_id) {
                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ \Illuminate\Support\Facades\URL::route('clubs.changeCities') }}',
                    data: {
                        state_id: state_id
                    },
                    dataType: 'json',
                    success: function (data) {
                        cities.empty();
                        $.each(data.cities, function (index, city) {
                            $('<option>').val(city.id).text(city['Title']).appendTo(cities);
                        })
                    },
                    error: function (data) {
                        console.log('Error:', data.responseText);
                    }
                });
            }
            else
            {
//                    cities.empty();
//                    $('<option>').val('').text('Select State First').appendTo(cities);
            }
        });

        $(".sel-status").select2();



        geocoder = new google.maps.Geocoder();
        /*
             * autocomplete location search
             */
        var PostCodeid = '#search_location';
        $(function () {

            $( PostCodeid ).keyup(function( event ) {
                var  search_addr = $('.search_addr').val('');
                console.log("ddd="+PostCodeid);
                $('#parsley-id-27').show();
                if($(this).val()!=""){

                    $('#parsley-id-27').css("position","static");
                }else{
                    $('#parsley-id-27').css("position","absolute");
                }
            }).keydown(function( event ) {
                /* if ( event.which == 13 ) {
                   event.preventDefault();
                 }*/
            });


            $(PostCodeid).autocomplete({
                source: function (request, response) {
                    geocoder.geocode({
                        'address': request.term
                    }, function (results, status) {
                        response($.map(results, function (item) {

                            if (results[0]) {
                                var address = results[0].formatted_address;
                                var city = state = country = pin = '';
                                $(results[0].address_components).each(function(index,value){
                                    //console.log(value.types[0], value.long_name);
                                    if('locality' == value.types[0])
                                        city = value.long_name;
                                    if('administrative_area_level_1' == value.types[0])
                                        state = value.long_name;
                                    if('country' == value.types[0])
                                        country = value.long_name;
                                    if('postal_code' == value.types[0])
                                        pin = value.long_name;
                                });
                                /*var pin = results[0].address_components[results[0].address_components.length - 1].long_name;
                                var country = results[0].address_components[results[0].address_components.length - 2].long_name;
                                var state = results[0].address_components[results[0].address_components.length - 3].long_name;
                                var city = results[0].address_components[results[0].address_components.length - 4].long_name;*/
                                document.getElementById('country').value = country;
                                document.getElementById('state').value = state;
                                document.getElementById('city').value = city;
                                document.getElementById('zip').value = pin;
                            }



                            return {
                                label: item.formatted_address,
                                value: item.formatted_address,
                                lat: item.geometry.location.lat(),
                                lon: item.geometry.location.lng()
                            };
                        }));
                    });
                },
                select: function (event, ui) {
                    $(PostCodeid).val(ui.item.value);
                    $('.search_addr').val(ui.item.value);
                    $('#parsley-id-27').hide();
                    $('.search_latitude').val(ui.item.lat);
                    $('.search_longitude').val(ui.item.lon);
                    /* var latlng = new google.maps.LatLng(ui.item.lat, ui.item.lon);
                     marker.setPosition(latlng);
                     initialize();*/
                }
            });
        });



    });


    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>




@endsection
