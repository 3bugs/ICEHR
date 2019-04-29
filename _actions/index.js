import { ADD_ARTICLE } from "../_constants/action-types";
export function addArticle(payload) {
    return { type: ADD_ARTICLE, payload };
}