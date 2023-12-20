<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Indovision</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .upload {
            border: 2px dashed black;
            border-radius: 15px;
            margin: 15px;
            padding: 10px;
            width: 90%;
            display: flex;
            justify-content: center;
            box-shadow: 0px 6px 15px black;
        }

        input {
            margin-top: 10px;
            margin-bottom: 10px;
            box-shadow: 0px 6px 15px black;
            border: 1px solid grey;
            padding: 8px;
        }

        .mdb-color.darken-3 {
            background-color: #1C2A48 !important;
        }
    </style>
</head>

<body>
    <div class="container">
        @if (Session::has('success'))
        <div class="alert alert-success m-5">
            {{ session()->get('success') }}
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-danger m-5">
            {{ session()->get('error') }}
        </div>
        @endif

        <center>
            <div class="upload mb-5">
                <form action="{{ route('process-file') }}" enctype="multipart/form-data" method="post" class="row">
                    @csrf
                    <label for="formFileSm" class="form-label">Select PDF or docx to extract data</label>
                    <div class="form-group">
                        <input class="form-control form-control-sm @error('uploadFileName') is-invalid @enderror"
                            id="formFileSm" type="file" name="uploadFileName" accept=".pdf, .docx"><br>
                        @error('uploadFileName')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror

                    </div>
                    <div class="form-group">
                        <button class="btn btn-info form-control" type="submit">Submit form</button>
                    </div>
                </form>

            </div>
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h2>Show Data</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive-md">
                        {{-- Table --}}
                        <table class="table table-hover">
                            {{-- Table head --}}
                            <thead class="mdb-color darken-3">
                                <tr class="text-white">
                                    <th>#</th>
                                    <th>File</th>
                                    <th>Origin File Name</th>
                                    <th>File extension</th>
                                    <th>Content</th>

                                </tr>
                            </thead>
                            {{-- Table head --}}
                            {{-- Table body --}}
                            <tbody>
                                @if (count($data) > 0)
                                @foreach ($data as $key => $item)
                                <tr>
                                    <th scope="row">{{ $key+1 }}</th>
                                    <td><a href="{{ asset($item->filename) }}" class="btn btn-info" target="blank">View
                                            File</a></td>
                                    <td>{{ $item->orig_filename }}</td>
                                    <td>{{ $item->extension }}</td>
                                    <td><a href="{{ route('edit.content', encrypt($item->id)) }}"
                                            class="btn btn-info">View/Update</a></td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                            {{-- Table body --}}
                        </table>
                        {{-- Table --}}
                    </div>
                </div>
            </div>

        </center>
        {{-- Start:: Create form for update content --}}
        @if (isset($edit) && !empty($edit))
        <form class="form row" action="{{ route('update.content', encrypt($edit->id)) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h2>Update Content</h2>
                </div>
                <div class="card-body">
                    <div class="form-group text-left">
                        <label for="content">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" rows="10" name="content"
                            required>{{ $edit->content }}</textarea>
                    </div>
                    @error('content')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success form-control">Update</button>
                </div>
            </div>
        </form>
        @endif
        {{-- End:: Form --}}
    </div>

</body>

</html>