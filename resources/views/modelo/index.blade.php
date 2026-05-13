<form action="{{ route('modelo.procesar') }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf

    <div>
        <label>Materia</label>
        <input type="text" name="materia" required>
    </div>

    <br>

    <div>
        <label>Corte</label>

        <select name="corte" required>
            <option value="1">Corte 1</option>
            <option value="2">Corte 2</option>
            <option value="3">Corte 3</option>
        </select>
    </div>

    <br>

    <div>
        <label>Porcentaje Nota 1</label>
        <input type="number"
               step="0.01"
               name="p1"
               required>
    </div>

    <br>

    <div>
        <label>Porcentaje Nota 2</label>
        <input type="number"
               step="0.01"
               name="p2"
               required>
    </div>

    <br>

    <div>
        <label>Porcentaje Nota 3</label>
        <input type="number"
               step="0.01"
               name="p3"
               required>
    </div>

    <br>

    <div>
        <label>Archivo Excel</label>
        <input type="file"
               name="excel"
               accept=".xlsx,.xls"
               required>
    </div>

    <br>

    <button type="submit">
        Generar PDF
    </button>

</form>