<div class="input-group number-spinner">
    <button class="btn btn-outline-primary" data-dir="up" dusk="{{ $id }}up">
        <i class="fas fa-plus"></i>
    </button>
    <input id="{{ $id }}" type="text" class="form-control align-center" name="{{ $id }}" value="0"/>
    <label for="{{ $id }}" class="visually-hidden">Type number</label>
    <button class="btn btn-outline-primary" data-dir="dwn" dusk="{{ $id }}down">
        <i class="fas fa-minus"></i>
    </button>
</div>
