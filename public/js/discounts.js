window.addEventListener('load', () => {
    // No-op
})

const addCategoryDiscount = () => {

    const taxonomy = document.getElementById('taxonomySelect').value
    const discount = document.getElementById('taxonomyDiscount')

    const discountInputs = document.querySelectorAll('.discountInput')
    const discountCategories = document.querySelectorAll('.discountCategory')
    // const discount = document.getElementById(`dto${inputId}`)
    if(taxonomy === 'Todos'){
        discountInputs.forEach(input => {
            input.value = parseInt(input.value) + parseInt(discount.value)
        })
        return;
    }

    discountInputs.forEach(discountInput => {
        const groupOfCategories = [...discountCategories].find(c => {
            const dpi = discountInput.getAttribute('data-product-id')
            return c.getAttribute('data-product-id') === dpi
        })

        const anyCategory = [...groupOfCategories.childNodes].some(a => {
            return a.innerHTML === taxonomy
        })
        if(anyCategory) {
            discountInput.value = parseInt(discountInput.value) + parseInt(discount.value)
        }
    })
}

const clearDiscounts = () => {
    const discountInputs = document.querySelectorAll('.discountInput')
    discountInputs.forEach(input => {
        input.value = 0
    })
}

const onClickDeleteProduct = (id) => {
    const action = "/removeProduct/" + id
    const form = document.getElementById('removeProductForm')
    form.action = action
    form.submit()
}
