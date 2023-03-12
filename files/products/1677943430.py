import requests
import random
import time
import STikTok
from urllib.parse import urlencode


class Registry():

    def _get_device(self):
        device = STikTok.Device_Genrator()
        STikTok.CaptchaSolver(
            did=device["device_id"], iid=device["install_id"])
        return device

    def _validate_account_params(self):
        device = self._get_device()
        return urlencode({
            "residence": "SA",
            "device_id": device["device_id"],
            "os_version": "14.4",
            "iid": device["install_id"],
            "app_name": "musical_ly",
            "locale": "en",
            "ac": "WIFI",
            "sys_region": "SA",
            "js_sdk_version": "1.77.0.2",
            "version_code": "21.1.0",
            "channel": "App Store",
            "vid": "7094F26A-EC10-45E4-8854-5D0616167B08",
            "op_region": "SA",
            "tma_jssdk_version": "1.77.0.2",
            "os_api": "18",
            "idfa": "D2CF453D-6981-4F32-A0EB-7A200FED8504",
            "device_platform": "ipad",
            "device_type": "iPad11,6",
            "openudid": device["openudid"],
            "account_region": "",
            "tz_name": "Asia/Riyadh",
            "tz_offset": "10800",
            "app_language": "en",
            "current_region": "SA",
            "build_number": "211023",
            "aid": "1233",
            "mcc_mnc": "",
            "screen_width": "1620",
            "uoo": "1",
            "content_language": "",
            "language": "en",
            "cdid": device["cdid"],
            "app_version": "21.1.0"
        })

    def _register_account(self):
        birthday = f"birthday=1986-01-02"
        password = "sa"
        username = "sa"
        deviceid = random.randrange(7115501749285094914, 7195501749285094914)
        STikTok.CaptchaSolver(did=deviceid, iid=0)
        params = STikTok.Sign(f"aid=143243&device_id={deviceid}&verifyFp=verify_lbtcmozr_SV8EAMJv_poB2_4xJD_9klD_JPCNTTpXYuy2&webcast_language=ar&msToken={STikTok.msToken(None)}",
                              "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36")
        url = f'https://api22-normal-c-useast1a.tiktokv.com/    /?'+params
        r = requests.post(url, headers={
            'Cookie': 'passport_csrf_token=f33fe49dfb8ce884cd32178851b1748d; passport_csrf_token_default=f33fe49dfb8ce884cd32178851b1748d; _ttp=2M4PmllcNyIbTzfZEsBEsdbKN7F; _ga=GA1.1.1792583342.1677111944; _ga_BZBQ2QHQSP=GS1.1.1677111944.1.0.1677111944.0.0.0; _tt_enable_cookie=1; _fbp=fb.1.1677111944897.1268049890; tta_attr_id=0.1677111961.7203141025164951554; tta_attr_id_mirror=0.1677111961.7203141025164951554; d_ticket=7e3c8c72feec7785e650b01cb8e7b1e0e6390; passport_auth_status=3d73aa1e7522454d1bc8d9033023d483%2C091289b46d7f6172f035696f1b0d8189; passport_auth_status_ss=3d73aa1e7522454d1bc8d9033023d483%2C091289b46d7f6172f035696f1b0d8189; odin_tt=eb16197e509e59efc46116f003447693023b74337841b12743c3690f48206fdb4af77b7cffca5ee4ea03ff1f7551c7cff706ffeec766fd8d91e224067e1361ca60651e8229a236ed5b9f883e1cd257d6; tt_csrf_token=WVAMaCmW-AQbaQwyQOQMdjnuBXSqq-_9acTs; tt_chain_token=UK1fD8bsCDmS/L6gxfU3lg==; bm_sz=B16FAB41181FA01267E91A2564972CE7~YAAQHzNWsklFS4mGAQAASyuDqBJlsjHp4Srd4l9PlKgl90NtlTZVXnAGGHJE0eI6fsgMt0qzoTLu5rKu4qaGxexSOTSncNdA8biVYsML61wiXtugZVPNW4kU25d4Bg7LZXniK4rXD8oNtIU6fpxalflVne+/bgr112vlMgq19fgwFWZHZHdqGhSoM5ME+FaqTnm5DDqqegy6YfAK2ffz5Gb9KPmqxUL3UzMvcReLMWe+EnunPcsMbodtIz6RGFGpzBAyscs00HYWGsed3pv9cGtvHhXVibaTqgjrpN7r3YLK0As=~3294256~4273730; ak_bmsc=C0B521DB9BD673F1A380097A3B30F6F3~000000000000000000000000000000~YAAQHzNWsk1FS4mGAQAASjGDqBKOkhRJoN0XJOwjqTMhsF6APl8pFREVaowvi9x6gxtxtXKabFR2ouA6UaJWkStgBU1iEkvnF3kdEGwjUz4NyXgR2Gbmh3Ab9uD710f3+uP92eMBem9JKkUDLAXsY5i86F1PTOpcSa6HlMxUFp0pZn52065p7k51bNma9tz6nnIIb1a10NA2CwkhfGXZBqILi6mJUzOMzrxZq7HqlhDwm14SO4nx1LVftkwEVAntMjm2qyO/FligMjzaWCr4RXxgv/c1yVxtZBWQpa60GrraXdW+umol3Ga8dlgs7oCQB7JDKj/5xop/8FlsF/QtmHhe3bISlIAUy7vacfG9nCHReuIV6Nb80eWTpUspE/bWKnmP7qfJKsy4ekjFF8605gaLyyNORFg8EGFN57W8+wrm7B1sI9XhVLD59ZmwMySWCUYn1LS1QbONznTSKg+CVi7hkwCZdyspVE1STqCFgHZsTbRfA2XF13XRrDc=; ttwid=1%7CPTzEYztnqsAdNZPYNNvGSzv_MoLITYEuNK-K_tK3U0M%7C1677864415%7Cd42ac3046de9cb0e97f068753e4f6bcd1a35eda26050fa38c117801ea6dd4adc; _abck=A05D031EB5ED82CA3E0BFCF87724E585~-1~YAAQTzNWstZFPImGAQAA7myDqAmTKZzLlObkPmjMGNTPMeBH/abX0OV8aksuClHrA0lsCI2MJg7mXooHG0n+Zt4/HNO53pFmcgvZ3j9jmSFFh7ZD74gG9GKspI6zu1w15lYkJmmsAj+ZhiUJRjmSjqCKWVmC2zYx34cqiW9BFxzUai0MfO8+zFUSNld1aqj/Z0TIMCcKsf9lGiYe7HIAgJludzRuAlyIhfSEysnAnU0tlPAGjUZb8wvhSmXtxDS6Bbqt8Ar+sD1xxRnTDBmQ7nobicsErzq214igUaSlCFS6rlPzrsk68J9YYVbeMOtbWH9sjm0XpqrlNL567HeF0S0nH+36T7LRAopC52MvROSY3mGJ/Qhp0ACqEA3CCAQJHAblYv2sPvqgYA==~-1~-1~-1; msToken=WH73jRjcC66ctaA8ScL_vY13KnWny8GcvA3XlhWPlvujGES9BniZ3g1SC9NO7eVXlM06IKraFAPD9-_WVjUK8yV4b93tQD-rtVt3pPrjGUsGcTxttzqRvbI6d7Wg1caf2lUOXvw=',
            'Content-Type': 'application/x-www-form-urlencoded', "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36",
                          "Referer": "https://www.tiktok.com/"}, data=f'mix_mode=1&username=71607671&password=7676&aid=1459&is_sso=false&account_sdk_source=web&region=SA&language=ar&fixed_mix_mode=1', verify=True, timeout=3, allow_redirects=True).json()
        print(r)


a = Registry()
a._register_account()
