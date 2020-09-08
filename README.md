# auth.gg-wrapper
a api wrapper for auth.gg

This is a simple wrapper for auth.gg so you can use your own server for hosting.
To contact their server we are using their api.

# Use

First you got to make a networking object (i didn't  make get request found on uc)
```
class networking
{
public:
	string replaceAll(string subject, const string& search,
		const string& replace) {
		size_t pos = 0;
		while ((pos = subject.find(search, pos)) != string::npos) {
			subject.replace(pos, search.length(), replace);
			pos += replace.length();
		}
		return subject;
	}

	string DownloadString(string URL) {
		HINTERNET interwebs = InternetOpenA("Mozilla/5.0", 1, NULL, NULL, NULL);
		HINTERNET urlFile;
		string rtn;
		if (interwebs) {
			urlFile = InternetOpenUrlA(interwebs, URL.c_str(), NULL, NULL, NULL, NULL);
			if (urlFile) {
				char buffer[2000];
				DWORD bytesRead;
				do {
					InternetReadFile(urlFile, buffer, 2000, &bytesRead);
					rtn.append(buffer, bytesRead);
					memset(buffer, 0, 2000);
				} while (bytesRead);
				InternetCloseHandle(interwebs);
				InternetCloseHandle(urlFile);
				string p = replaceAll(rtn, "|n", "\r\n");
				return p;
			}
		}
		InternetCloseHandle(interwebs);
		string p = replaceAll(rtn, "|n", "\r\n");
		return p;
	}

};



```
