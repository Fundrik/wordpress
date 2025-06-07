import { useEntityProp } from '@wordpress/core-data';

export function usePostMetaField(postType, fieldName) {

	const [meta, setMeta] = useEntityProp('postType', postType, 'meta');

	const value = meta?.[fieldName];

	const setValue = (newValue) => {
		setMeta({ ...meta, [fieldName]: newValue });
	};

	return [value, setValue];
}