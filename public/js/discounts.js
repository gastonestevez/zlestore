window.addEventListener('load', () => {
    let discountNumber = 1
    const discountContainer = document.getElementById('discounts')
    const addDiscountButton = document.getElementById('addDiscountButton')
    const removeDiscountsButton = document.getElementById('removeDiscountsButton')
    const discountElements = discountContainer.innerHTML
    discountContainer.innerHTML = ''
    discountContainer.removeAttribute('hidden')

    addDiscountButton.addEventListener('click', () => {
        const newElement = document.createElement('div')
        newElement.innerHTML = discountElements.replace('{DISCOUNT_NUMBER}', discountNumber)
        discountContainer.appendChild(newElement)
        discountNumber++
        removeDiscountsButton.removeAttribute('hidden')
    })

    removeDiscountsButton.addEventListener('click', () => {
        removeDiscountsButton.setAttribute('hidden', '')
        discountContainer.innerHTML = ''
        discountNumber = 1
    })
})