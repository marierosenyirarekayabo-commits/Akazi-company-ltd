<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Worker - AKAZI COMPANY LTD</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f7fb;
            margin: 0;
            padding: 40px;
        }

        .form-box {
            width: 500px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #2563eb;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 7px;
        }

        textarea {
            height: 100px;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 25px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 7px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

<div class="form-box">

    <h1>Register Worker</h1>

    <form action="save_worker.php"
          method="POST"
          enctype="multipart/form-data">

        <label>Full Name</label>
        <input type="text"
               name="full_name"
               required>

        <label>Age</label>
        <input type="number"
               name="age"
               min="18"
               required>

        <label>Umurimo akora</label>
        <input type="text"
               name="job"
               required>

        <label>Certificate</label>
        <input type="file"
               name="certificate"
               accept=".pdf,.jpg,.jpeg,.png">

        <label>Abatangabuhamya</label>
        <textarea name="references_info"></textarea>

        <label>Aho aturuka</label>
        <input type="text"
               name="location"
               required>

        <label>Phone</label>
        <input type="tel"
               name="phone"
               required>

        <label>Email</label>
        <input type="email"
               name="email"
               required>

        <button type="submit">
            Register Worker
        </button>

    </form>

</div>

</body>
</html>