// src/utils/confirmation.js
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

/**
 * Display a confirmation dialog using SweetAlert2.
 *
 * @param {string} title - The title of the confirmation dialog.
 * @param {string} text - The message shown in the dialog body.
 * @returns {Promise<SweetAlertResult>} - Resolves with the user's response.
 */
export const showConfirmation = (
  title = 'Are you sure?',
  text = "You won't be able to revert this!"
) => {
  return Swal.fire({
    title,
    text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#1F2937', // Tailwind gray-800
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  });
};
