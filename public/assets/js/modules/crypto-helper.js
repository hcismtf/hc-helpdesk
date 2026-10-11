/**
 * Global Crypto & LocalStorage Helper
 * 
 * Provides simple, transparent encryption & decryption for sensitive client-side storage.
 * Designed for storing user references (e.g. 'userRef') and general data safely in localStorage.
 * 
 * Usage:
 *   // 1. UserRef Storage
 *   CryptoHelper.setUserRef(userDataObject);
 *   const user = CryptoHelper.getUserRef();
 * 
 *   // 2. Generic Encrypt / Decrypt
 *   const cipher = CryptoHelper.encrypt({ any: 'payload' });
 *   const plain = CryptoHelper.decrypt(cipher);
 * 
 *   // 3. Global Shortcuts
 *   window.encryptData(data);
 *   window.decryptData(cipher);
 *   window.getUserRef();
 *   window.setUserRef(data);
 */

(function (global) {
  'use strict';

  // Secret Application Salt Key (Dapat disesuaikan atau dioverride)
  var DEFAULT_SECRET = 'MTF_HC_HELPDESK_SECURE_STORAGE_KEY_2026_#9981';

  var CryptoHelper = {
    /**
     * Mengenkripsi data (string, object, array, number) menjadi string terenkripsi aman
     * @param {any} data Data yang akan dienkripsi
     * @param {string} [secretKey] Kunci enkripsi opsional
     * @returns {string} String ciphertext Base64
     */
    encrypt: function (data, secretKey) {
      if (data === undefined || data === null) {
        return '';
      }

      var key = secretKey || DEFAULT_SECRET;
      var rawString = typeof data === 'object' ? JSON.stringify(data) : String(data);

      try {
        // 1. Generate random 8-character hex salt
        var salt = '';
        var hexChars = '0123456789abcdef';
        for (var i = 0; i < 8; i++) {
          salt += hexChars.charAt(Math.floor(Math.random() * hexChars.length));
        }

        // 2. Multi-round keystream expansion
        var combinedKey = salt + key + salt;
        var keyLen = combinedKey.length;
        var output = [];

        // UTF-8 encode raw string
        var utf8Bytes = [];
        for (var j = 0; j < rawString.length; j++) {
          var charCode = rawString.charCodeAt(j);
          if (charCode < 0x80) {
            utf8Bytes.push(charCode);
          } else if (charCode < 0x800) {
            utf8Bytes.push(0xc0 | (charCode >> 6), 0x80 | (charCode & 0x3f));
          } else if (charCode < 0xd800 || charCode >= 0xe000) {
            utf8Bytes.push(0xe0 | (charCode >> 12), 0x80 | ((charCode >> 6) & 0x3f), 0x80 | (charCode & 0x3f));
          } else {
            j++;
            charCode = 0x10000 + (((charCode & 0x3ff) << 10) | (rawString.charCodeAt(j) & 0x3ff));
            utf8Bytes.push(
              0xf0 | (charCode >> 18),
              0x80 | ((charCode >> 12) & 0x3f),
              0x80 | ((charCode >> 6) & 0x3f),
              0x80 | (charCode & 0x3f)
            );
          }
        }

        // 3. Byte substitution & dynamic permutation cipher
        for (var k = 0; k < utf8Bytes.length; k++) {
          var keyByte = combinedKey.charCodeAt(k % keyLen);
          var cipherByte = (utf8Bytes[k] ^ keyByte ^ ((k * 31 + 17) & 0xff)) & 0xff;
          output.push(cipherByte);
        }

        // 4. Encode salt + ciphertext to Base64
        var binaryStr = '';
        for (var m = 0; m < output.length; m++) {
          binaryStr += String.fromCharCode(output[m]);
        }

        var base64Data = btoa(binaryStr);
        return 'enc::' + salt + '::' + base64Data;
      } catch (err) {
        console.error('[CryptoHelper] Encryption error:', err);
        return '';
      }
    },

    /**
     * Mendekripsi string ciphertext kembali ke data aslinya (Object atau String)
     * @param {string} cipherText String ciphertext hasil enkripsi
     * @param {string} [secretKey] Kunci enkripsi opsional
     * @returns {any} Objek asli atau string asli, atau null jika gagal
     */
    decrypt: function (cipherText, secretKey) {
      if (!cipherText || typeof cipherText !== 'string') {
        return null;
      }

      var key = secretKey || DEFAULT_SECRET;

      try {
        var parts = cipherText.split('::');
        if (parts.length !== 3 || parts[0] !== 'enc') {
          // Jika bukan format terenkripsi enc::, coba parse JSON langsung atau kembalikan teks biasa
          try {
            return JSON.parse(cipherText);
          } catch (e) {
            return cipherText;
          }
        }

        var salt = parts[1];
        var base64Data = parts[2];
        var combinedKey = salt + key + salt;
        var keyLen = combinedKey.length;

        var binaryStr = atob(base64Data);
        var bytes = [];
        for (var i = 0; i < binaryStr.length; i++) {
          bytes.push(binaryStr.charCodeAt(i));
        }

        var decryptedBytes = [];
        for (var j = 0; j < bytes.length; j++) {
          var keyByte = combinedKey.charCodeAt(j % keyLen);
          var plainByte = (bytes[j] ^ keyByte ^ ((j * 31 + 17) & 0xff)) & 0xff;
          decryptedBytes.push(plainByte);
        }

        // UTF-8 decode bytes
        var resultStr = '';
        var idx = 0;
        while (idx < decryptedBytes.length) {
          var b1 = decryptedBytes[idx++];
          if (b1 < 0x80) {
            resultStr += String.fromCharCode(b1);
          } else if (b1 > 0xbf && b1 < 0xe0) {
            var b2 = decryptedBytes[idx++];
            resultStr += String.fromCharCode(((b1 & 0x1f) << 6) | (b2 & 0x3f));
          } else if (b1 > 0xdf && b1 < 0xf0) {
            var b2_ = decryptedBytes[idx++];
            var b3 = decryptedBytes[idx++];
            resultStr += String.fromCharCode(((b1 & 0x0f) << 12) | ((b2_ & 0x3f) << 6) | (b3 & 0x3f));
          } else {
            var b2__ = decryptedBytes[idx++];
            var b3_ = decryptedBytes[idx++];
            var b4 = decryptedBytes[idx++];
            var codePoint = (((b1 & 0x07) << 18) | ((b2__ & 0x3f) << 12) | ((b3_ & 0x3f) << 6) | (b4 & 0x3f)) - 0x10000;
            resultStr += String.fromCharCode(0xd800 + (codePoint >> 10), 0xdc00 + (codePoint & 0x3ff));
          }
        }

        // Coba parse JSON jika data aslinya adalah objek
        try {
          return JSON.parse(resultStr);
        } catch (e) {
          return resultStr;
        }
      } catch (err) {
        console.error('[CryptoHelper] Decryption error:', err);
        return null;
      }
    },

    /**
     * Menyimpan data user dari tabel users ke localStorage dengan key 'userRef' (Terenkripsi)
     * @param {Object} userData Data user yang akan disimpan
     * @returns {boolean} Status keberhasilan
     */
    setUserRef: function (userData) {
      if (!userData) return false;
      try {
        var encrypted = this.encrypt(userData);
        localStorage.setItem('userRef', encrypted);
        return true;
      } catch (e) {
        console.error('[CryptoHelper] Gagal menyimpan userRef ke localStorage:', e);
        return false;
      }
    },

    /**
     * Mengambil dan mendekripsi data user 'userRef' dari localStorage
     * @returns {Object|null} Objek user yang sudah didekripsi
     */
    getUserRef: function () {
      try {
        var encrypted = localStorage.getItem('userRef');
        if (!encrypted) return null;
        return this.decrypt(encrypted);
      } catch (e) {
        console.error('[CryptoHelper] Gagal mengambil userRef dari localStorage:', e);
        return null;
      }
    },

    /**
     * Menghapus 'userRef' dari localStorage
     */
    clearUserRef: function () {
      try {
        localStorage.removeItem('userRef');
      } catch (e) { }
    }
  };

  // Expose ke global window object
  global.CryptoHelper = CryptoHelper;
  global.encryptData = CryptoHelper.encrypt.bind(CryptoHelper);
  global.decryptData = CryptoHelper.decrypt.bind(CryptoHelper);
  global.getUserRef = CryptoHelper.getUserRef.bind(CryptoHelper);
  global.setUserRef = CryptoHelper.setUserRef.bind(CryptoHelper);
  global.clearUserRef = CryptoHelper.clearUserRef.bind(CryptoHelper);

})(typeof window !== 'undefined' ? window : this);
