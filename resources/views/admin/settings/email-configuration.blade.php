    <div class="card border">
        <div class="card-body">
            <form action="{{route('admin.email-setting-update')}}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" value="{{$emailSettings->email}}">
                </div>

                <div class="form-group mb-3">
                    <label>Mail Host</label>
                    <input type="text" class="form-control" name="host" value="{{$emailSettings->host}}">
                </div>


                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Smtp username</label>
                            <input type="text" class="form-control" name="username" value="{{$emailSettings->username}}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Smtp password</label>
                            <input type="text" class="form-control" name="password" value="{{$emailSettings->password}}">
                        </div>
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mail port</label>
                            <input type="text" class="form-control" name="port" value="{{$emailSettings->port}}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mail Encryption</label>
                            <select name="encryption" id="" class="form-control">
                                    <option {{$emailSettings->encryption == 'tls' ? 'selected' : ''}} value="tls">TLS</option>
                                    <option {{$emailSettings->encryption == 'ssl' ? 'selected' : ''}} value="ssl">SSL</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
