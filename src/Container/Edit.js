import {compose} from '@wordpress/compose'
import {withDispatch, withSelect} from '@wordpress/data'
import Edit from '../Component/Edit';
import apiFetch from "@wordpress/api-fetch";

export const mapSelectToProps = async (select) => {
    return {}
}

export const mapDispatchToProps = (dispatch) => {
    return {}
}

export default compose(
    [
        withSelect(mapSelectToProps),
        withDispatch(mapDispatchToProps),
    ]
)(Edit)
