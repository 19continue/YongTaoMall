import { get, post } from "./MyAxios";

export const getAllGoods = (params) => post('goods/getAll', params);