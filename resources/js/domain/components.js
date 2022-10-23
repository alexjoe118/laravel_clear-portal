import { camelCase } from 'lodash';

import Button from '~/components/button';
import FormFieldsPersonal from '~/components/form-fields-personal';
import FormFieldsLoanRequest from '~/components/form-fields-loan-request';
import GridBoxes from '~/components/grid-boxes';
import inputAddress from '~/components/input-address';
import inputCheckbox from '~/components/input-checkbox';
import InputUploadFile from '~/components/input-upload-file';
import InputUploadImage from '~/components/input-upload-image';
import InputRepeater from '~/components/input-repeater';
import InputSignature from '~/components/input-signature';
import Modal from '~/components/modal';
import OpenApprovalsSampler from '~/components/open-approvals-sampler';
import PreviewOptions from '~/components/preview-options';
import ResourceListing from '~/components/resource-listing';

/**
 * Available components.
 *
 * @var {object}
 */
const componentsList = {
	'button': Button,
	'form-fields-personal': FormFieldsPersonal,
	'form-fields-loan-request': FormFieldsLoanRequest,
	'grid-boxes': GridBoxes,
	'input-address': inputAddress,
	'input-checkbox': inputCheckbox,
	'input-upload-file': InputUploadFile,
	'input-upload-image': InputUploadImage,
	'input-repeater': InputRepeater,
	'input-signature': InputSignature,
	'modal': Modal,
	'open-approvals-sampler': OpenApprovalsSampler,
	'preview-options': PreviewOptions,
	'resource-listing': ResourceListing
};

/**
 * Initialized components.
 *
 * @var {object}
 */
const components = {};

/**
 * Setup the existing components on the page.
 *
 * @param {HTMLElement} parent
 */
export function setupComponents ( parent = document ) {
	Object.keys( componentsList ).forEach( componentSlug => {
		if ( ! components[ camelCase( componentSlug ) ]) {
			components[ camelCase( componentSlug ) ] = [];
		}

		// All component elements of this type on the page.
		const elements = [ ...parent.querySelectorAll( '.js-' + componentSlug ) ] || [];

		elements.forEach( element => {

			// Initialize the component if it does not already exist.
			if ( ! element.componentInitialized ) {
				const instance = new componentsList[ componentSlug ]( element );
				instance.setup();
				element.componentInitialized = true;

				// Append instance to initialized components list.
				components[ camelCase( componentSlug ) ].push( instance );
			}
		});
	});
}
