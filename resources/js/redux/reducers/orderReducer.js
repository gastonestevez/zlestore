const initialState = {
    order: null,
    id: null,
    warehouses: null,
}

export const FETCH_ORDER_ID =  'FETCH_ORDER_ID'
export const SET_ORDER = 'SET_ORDER'
export const SET_WAREHOUSES = 'SET_WAREHOUSES'

export const fetchOrderId = (id) => {
    return {
        type: FETCH_ORDER_ID,
        payload: id,
    }
}

export const setOrder = (order) => {
    return {
        type: SET_ORDER,
        payload: order
    }
}

export const setWarehouses = (warehouses) => {
    return {
        type: SET_WAREHOUSES,
        payload: warehouses
    }
}

const orderReducer = (state = initialState, {type, payload}) => {
    switch(type){
        case FETCH_ORDER_ID:
            return {
                ...state,
                id: payload
            }
        case SET_ORDER:
            return {
                ...state,
                order: payload
            }
        case SET_WAREHOUSES:
            return {
                ...state,
                warehouses: payload
            }
        default:
            return initialState
    }
}

export default orderReducer