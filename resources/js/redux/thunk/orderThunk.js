import { fetchOrderId, setOrder, setWarehouses } from '../reducers/orderReducer'

export const fetchOrderById = (id) => async (dispatch) => {
    try {
        const url = `http://localhost:8000/api/orders/${id}`
        const response = await axios.get(url)
        dispatch(fetchOrderId(id))
        dispatch(setOrder(response.data.order))
    } catch (e) {
        console.error(e)
    }
}

export const fetchWarehouses = () => async (dispatch) => {
    const url = `http://localhost:8000/api/warehouses`
    const response = await axios.get(url)
    dispatch(setWarehouses(response.data.warehouses))
}