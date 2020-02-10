@extends('layouts.app')

@section('content')
    <!-- <customer-index></customer-index> -->



    <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <!-- Page content -->
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">Customer List</h3>
                                    </div>
                                    <div class="col text-right">
                                        <a href="{{ url('/customers/create')}}" class="btn btn-sm btn-primary">Add New</a>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                            <form method="get">
                                <div class="row ml-2">
                                        <div class="col-md-4">
                                            <label for="search" class="form-control-label">Search</label>
                                            <input type="text" name="search" value="{{$search}}" placeholder="Search" class="form-control">
                                        </div>
                                        <div class="col-md-2 mt-4">
                                            <input type="submit" value="Search" class="btn btn-sm btn-primary">
                                        </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Street</th>
                                    <th scope="col">Town or City</th>
                                    <th scope="col">Province</th>
                                    <th scope="col">Google Map Address</th>
                                    <th scope="col">Classification</th>
                                    <th scope="col">Telephone 1</th>
                                    <th scope="col">Telephone 2</th>
                                    <th scope="col">Fax number</th>
                                    <th scope="col">Remarks</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if(count($customers) > 0 )
                                        @foreach($customers as $customer)
                                        <tr>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                        <a class="dropdown-item" href="{{ url('/customers-edit/' . $customer->id)}}">Edit</a>
                                                        <!-- <a class="dropdown-item" href="#deleteModal" data-toggle="modal" @click="getCustomerId(customer.id)">Delete</a> -->
                                                        <delete-customer :customer-id="{{ $customer->id }}"></delete-customer>
                                                        <a class="dropdown-item" href="https://www.google.com/maps/place/{{ $customer->lat .','. $customer->lng}}" target="_blank">View address</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td align="center">{{ $customer->customer_code}}</td>
                                            <td align="left">{{ $customer->name}}</td>
                                            <td align="left">{{ $customer->street}}</td>
                                            <td align="left">{{ $customer->town_city}}</td>
                                            <td align="left">{{ $customer->provinces ? $customer->provinces->name : "" }}</td>
                                            <td align="left">{{ $customer->google_address}}</td>
                                            <td align="left">{{ $customer->classifications ? $customer->classifications->name : ""}}</td>
                                            <td align="left">{{ $customer->telephone_1}}</td>
                                            <td align="left">{{ $customer->telephone_2}}</td>
                                            <td align="left">{{ $customer->fax_number}}</td>
                                            <td align="left">{{ $customer->remarks}}</td>
                                            
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="12" align="center">
                                                <strong>No Records Found!</strong>
                                            </td>
                                        </tr>
                                    @endif
                                    
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="col-md-6">
                                {{ $customers->links() }}
                            </div>
                            <div class="col-md-6">
                                Total Filtered Customer(s): {{ count($customers) }}
                                <br>
                                Total Customer(s): {{ $customers->total() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>
@endsection
