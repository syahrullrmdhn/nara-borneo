<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md mx-auto">
            <h1 class="text-2xl font-semibold mb-4 text-center">Silahkan Input No Dokumen</h1>
            <form action="proses_invoice.php" method="POST">
                <div class="mb-4">
                    <label for="nomor_dokumen" class="block text-sm font-medium text-gray-700">Nomor Dokumen Invoice</label>
                    <input type="text" name="nomor_dokumen" id="nomor_dokumen" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div class="text-center">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
