<style>
    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 10px;
    }

    th,
    td {
        border: 0px solid #ddd;
        padding: 15px;
        text-align: center;

    }

    th {
        background-color: #094040;
        color: white;
        height: 75px;
        width: 20%;
        /* Decrease the width of the "Website" column */
    }

    th:first-child {
        border-top-left-radius: 12px;
        /* Apply border-radius to the top-left corner of the "Website" column */
    }

    th:last-child {
        border-top-right-radius: 12px;
        /* Apply border-radius to the top-right corner of the "Pro" column */
    }

    .checkmark {
        display: flex;
        justify-content: center;
        /* Center the checkmark-circle image horizontally */
        align-items: center;
        /* Center the checkmark-circle image vertically */
    }

    .checkmark-basic img {
        margin-left: auto;
        /* Move the basic checkmark to the right */
    }
</style>